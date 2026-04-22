<?php

namespace App\Services;

use App\Models\Investor;
use App\Models\InvestorStageTransition;
use Carbon\Carbon;

class InvestorStageService
{
    /**
     * Gate requirements for each stage.
     * All conditions must pass before an investor can move to that stage.
     */
    protected array $stageRules = [

        // STAGE 2: Eligibility Confirmed
        'eligibility_confirmed' => [
            'target_commitment_amount' => ['min', 1000000],
            'is_professional_client'   => ['equals', true],
            'difc_dp_consent'          => ['equals', true],
        ],

        // STAGE 3: Portal Access Granted
        'portal_access_granted' => [
            'has_consent_record'        => ['equals', true],
            'has_introductory_meeting'  => ['equals', true],
            'subscription_signed_date'  => ['not_null'],
            'final_commitment_amount'   => ['greater_than', 0],
        ],

        // STAGE 4: KYC In Progress
        'kyc_in_progress' => [
            'kyc_status' => ['in', ['in_progress', 'submitted', 'under_review', 'complete']],
        ],

        // STAGE 5: KYC Completed / Approved
        'kyc_completed' => [
            'kyc_status'               => ['equals', 'complete'],
            'sanctions_check_passed'   => ['equals', true],
            'commitment_letter_signed' => ['equals', true],
        ],

        // STAGE 6: Funded / Active
        'funded' => [
            'bank_account_verified' => ['equals', true],
            'funded_amount'         => ['greater_than', 0],
        ],

        // STAGE 7: Monitored — Compliance Officer decision, no hard gates
        'monitored' => [],
    ];

    /**
     * Human-readable field names for missing requirement messages.
     */
    protected array $fieldLabels = [
        'target_commitment_amount' => 'Target Commitment Amount (min $1,000,000)',
        'is_professional_client'   => 'Professional Client Status confirmed',
        'difc_dp_consent'          => 'Initial DP notice provided to client',
        'agreed_confidentiality'   => 'NDA / Confidentiality implied (auto-set on Portal Access)',
        'has_consent_record'        => 'Consent record on file (investor submitted a document access request)',
        'has_introductory_meeting'  => 'Introductory Meeting held and logged',
        'subscription_signed_date' => 'Subscription Agreement signed & received',
        'final_commitment_amount'  => 'Final Commitment Amount entered',
        'kyc_status'               => 'KYC/AML documents uploaded and in review',
        'sanctions_check_passed'   => 'Sanctions Check passed',
        'commitment_letter_signed' => 'Commitment Letter signed',
        'bank_account_verified'    => 'Bank Account verified',
        'funded_amount'            => 'Funded Amount entered',
    ];

    /**
     * Check if investor meets all requirements for a target stage.
     */
    public function canMoveToStage(Investor $investor, string $targetStage): bool
    {
        if ($targetStage === 'prospect') {
            return true;
        }

        if (!isset($this->stageRules[$targetStage])) {
            return true;
        }

        foreach ($this->stageRules[$targetStage] as $field => $rule) {
            if (!$this->checkRule($investor->$field, $rule)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Move investor to a new stage, log the transition, trigger automation.
     */
    public function moveToStage(Investor $investor, string $newStage, ?string $reason = null, ?int $userId = null): bool
    {
        if (!$this->canMoveToStage($investor, $newStage)) {
            return false;
        }

        $oldStage  = $investor->stage;
        $oldStatus = $investor->status;

        $investor->stage  = $newStage;
        $investor->status = $this->getDefaultStatusForStage($newStage);
        $investor->save();

        InvestorStageTransition::create([
            'investor_id'        => $investor->id,
            'from_stage'         => $oldStage,
            'to_stage'           => $newStage,
            'from_status'        => $oldStatus,
            'to_status'          => $investor->status,
            'changed_by_user_id' => $userId ?? auth()->id(),
            'reason'             => $reason,
            'transitioned_at'    => Carbon::now(),
        ]);

        app(\App\Services\DataRoomService::class)->logActivity(
            $investor, null, null, 'stage_transition',
            [
                'from_stage'  => $oldStage,
                'to_stage'    => $newStage,
                'from_status' => $oldStatus,
                'to_status'   => $investor->status,
                'reason'      => $reason,
            ]
        );

        $this->triggerStageAutomation($investor, $newStage);

        return true;
    }

    /**
     * Return the default workflow status for a given stage.
     */
    protected function getDefaultStatusForStage(string $stage): string
    {
        return match ($stage) {
            'prospect'              => 'pending',
            'eligibility_confirmed' => 'in_review',
            'portal_access_granted' => 'in_review',
            'kyc_in_progress'       => 'in_review',
            'kyc_completed'         => 'qualified',
            'funded'                => 'qualified',
            'monitored'             => 'qualified',
            default                 => 'pending',
        };
    }

    /**
     * Trigger automatic actions when a stage is reached.
     */
    protected function triggerStageAutomation(Investor $investor, string $newStage): void
    {
        switch ($newStage) {

            case 'portal_access_granted':
                // Record PPM + NDA acknowledged (implied via PPM) and upgrade DR to Qualified
                $investor->update([
                    'ppm_acknowledged_date'         => $investor->ppm_acknowledged_date ?? Carbon::now(),
                    'agreed_confidentiality'        => true,
                    'acknowledged_ppm_confidential' => true,
                    'data_room_access_level'        => 'qualified',
                    'data_room_access_granted'   => true,
                    'data_room_access_granted_at' => Carbon::now(),
                ]);
                app(\App\Services\DataRoomService::class)->upgradeAccess(
                    $investor, 'qualified', 'Auto-upgraded on Portal Access Granted'
                );
                break;

            case 'kyc_completed':
                // Record approval date and approver
                $investor->update([
                    'kyc_completed_date'     => Carbon::now(),
                    'approved_date'          => Carbon::now(),
                    'approved_by_user_id'    => auth()->id(),
                ]);
                break;

            case 'funded':
                // Record funding & activation, upgrade DR to Subscribed, generate investor ID
                $investor->update([
                    'funding_date'           => $investor->funding_date ?? Carbon::now(),
                    'activated_date'         => Carbon::now(),
                    'data_room_access_level' => 'subscribed',
                    'reporting_access_granted' => true,
                ]);
                app(\App\Services\DataRoomService::class)->upgradeAccess(
                    $investor, 'subscribed', 'Auto-upgraded on Funded/Active'
                );
                if (!$investor->investor_id_number) {
                    $investor->update([
                        'investor_id_number' => $this->generateInvestorId($investor),
                    ]);
                }
                break;
        }
    }

    /**
     * Generate a unique investor ID string.
     */
    protected function generateInvestorId(Investor $investor): string
    {
        $year     = Carbon::now()->year;
        $sequence = str_pad($investor->id, 4, '0', STR_PAD_LEFT);

        return "INV-{$year}-{$sequence}";
    }

    /**
     * Try to auto-advance investor to the next stage if all gates are met.
     */
    public function autoAdvanceIfEligible(Investor $investor): bool
    {
        $progression = [
            'prospect'              => 'eligibility_confirmed',
            'eligibility_confirmed' => 'portal_access_granted',
            'portal_access_granted' => 'kyc_in_progress',
            'kyc_in_progress'       => 'kyc_completed',
            'kyc_completed'         => 'funded',
        ];

        if (!isset($progression[$investor->stage])) {
            return false;
        }

        $nextStage = $progression[$investor->stage];

        if ($this->canMoveToStage($investor, $nextStage)) {
            return $this->moveToStage($investor, $nextStage, 'Auto-advanced (all criteria met)');
        }

        return false;
    }

    /**
     * Return an array of human-readable missing requirement strings for a target stage.
     */
    public function getMissingRequirements(Investor $investor, string $targetStage): array
    {
        $missing = [];

        if (!isset($this->stageRules[$targetStage])) {
            return $missing;
        }

        foreach ($this->stageRules[$targetStage] as $field => $rule) {
            if (!$this->checkRule($investor->$field, $rule)) {
                $missing[] = $this->fieldLabels[$field]
                    ?? ucfirst(str_replace('_', ' ', $field)) . ' requirement not met';
            }
        }

        return $missing;
    }

    /**
     * Evaluate a single rule against a field value.
     */
    protected function checkRule(mixed $fieldValue, array $rule): bool
    {
        $operator = $rule[0];
        $value    = $rule[1] ?? null;

        return match ($operator) {
            'min'          => $fieldValue >= $value,
            'equals'       => $fieldValue === $value,
            'not_null'     => !is_null($fieldValue),
            'greater_than' => $fieldValue > $value,
            'in'           => in_array($fieldValue, (array) $value, true),
            default        => true,
        };
    }
}
