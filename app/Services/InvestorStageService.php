<?php

namespace App\Services;

use App\Models\Investor;
use App\Models\InvestorStageTransition;
use Carbon\Carbon;

class InvestorStageService
{
    /**
     * Stage validation rules
     */
    protected array $stageRules = [
        'eligibility_review' => [
            'target_commitment_amount' => ['min', 1000000], // $1M minimum
            'jurisdiction' => ['not_in', ['sanctioned_countries']], // Example
        ],
        'ppm_issued' => [
            'is_professional_client' => ['equals', true],
            'sanctions_check_passed' => ['equals', true],
        ],
        'kyc_in_progress' => [
            'ppm_acknowledged_date' => ['not_null'],
        ],
        'subscription_signed' => [
            'kyc_status' => ['equals', 'complete'],
            'sanctions_check_passed' => ['equals', true],
        ],
        'approved' => [
            'subscription_signed_date' => ['not_null'],
            'final_commitment_amount' => ['greater_than', 0],
        ],
        'funded' => [
            'approved_date' => ['not_null'],
            'bank_account_verified' => ['equals', true],
        ],
        'active' => [
            'funded_amount' => ['greater_than', 0],
        ],
    ];

    /**
     * Check if investor can move to target stage
     */
    public function canMoveToStage(Investor $investor, string $targetStage): bool
    {
        // Prospect can always be created
        if ($targetStage === 'prospect') {
            return true;
        }

        // Check if rules exist for this stage
        if (!isset($this->stageRules[$targetStage])) {
            return true; // No rules = allowed
        }

        $rules = $this->stageRules[$targetStage];

        foreach ($rules as $field => $rule) {
            [$operator, $value] = $rule;

            $fieldValue = $investor->$field;

            switch ($operator) {
                case 'min':
                    if ($fieldValue < $value) return false;
                    break;
                case 'equals':
                    if ($fieldValue !== $value) return false;
                    break;
                case 'not_null':
                    if (is_null($fieldValue)) return false;
                    break;
                case 'greater_than':
                    if ($fieldValue <= $value) return false;
                    break;
                case 'not_in':
                    // Example: check jurisdiction not in sanctioned list
                    // In real implementation, check against actual list
                    break;
            }
        }

        return true;
    }

    /**
     * Move investor to new stage
     */
    public function moveToStage(Investor $investor, string $newStage, ?string $reason = null, ?int $userId = null): bool
    {
        // Validate if move is allowed
        if (!$this->canMoveToStage($investor, $newStage)) {
            return false;
        }

        $oldStage = $investor->stage;
        $oldStatus = $investor->status;

        // Update investor stage
        $investor->stage = $newStage;
        
        // Auto-update status based on stage
        $investor->status = $this->getDefaultStatusForStage($newStage);
        
        $investor->save();

        // Log transition
        InvestorStageTransition::create([
            'investor_id' => $investor->id,
            'from_stage' => $oldStage,
            'to_stage' => $newStage,
            'from_status' => $oldStatus,
            'to_status' => $investor->status,
            'changed_by_user_id' => $userId ?? auth()->id(),
            'reason' => $reason,
            'transitioned_at' => Carbon::now(),
        ]);

        // Trigger automation based on stage
        $this->triggerStageAutomation($investor, $newStage);

        return true;
    }

    /**
     * Get default status for a stage
     */
    protected function getDefaultStatusForStage(string $stage): string
    {
        return match($stage) {
            'prospect' => 'pending',
            'eligibility_review' => 'in_review',
            'ppm_issued' => 'in_review',
            'kyc_in_progress' => 'in_review',
            'subscription_signed' => 'in_review',
            'approved' => 'qualified',
            'funded' => 'qualified',
            'active' => 'qualified',
            'monitored' => 'qualified',
            default => 'pending',
        };
    }

    /**
     * Trigger automation when stage changes
     */
    protected function triggerStageAutomation(Investor $investor, string $newStage): void
    {
        switch ($newStage) {
            case 'eligibility_review':
                // Could trigger: Send welcome email
                $investor->update([
                    'sanctions_checked_at' => Carbon::now(),
                ]);
                break;

            case 'ppm_issued':
                // Grant Data Room access (PROSPECT level)
                $investor->update([
                    'ppm_issued_date' => Carbon::now(),
                    'data_room_access_level' => 'prospect',
                    'data_room_access_granted' => true,
                    'data_room_access_granted_at' => Carbon::now(),
                ]);
                // TODO: Call DataRoomService to grant folder permissions
                break;

            case 'kyc_in_progress':
                // Upgrade Data Room access (QUALIFIED level)
                $investor->update([
                    'data_room_access_level' => 'qualified',
                ]);
                // TODO: Call DataRoomService to upgrade permissions
                break;

            case 'approved':
                $investor->update([
                    'approved_date' => Carbon::now(),
                    'approved_by_user_id' => auth()->id(),
                ]);
                break;

            case 'funded':
                $investor->update([
                    'funding_date' => Carbon::now(),
                ]);
                break;

            case 'active':
                // Upgrade to SUBSCRIBED Data Room access
                $investor->update([
                    'activated_date' => Carbon::now(),
                    'data_room_access_level' => 'subscribed',
                    'reporting_access_granted' => true,
                ]);
                
                // Generate investor ID if not exists
                if (!$investor->investor_id_number) {
                    $investor->update([
                        'investor_id_number' => $this->generateInvestorId($investor),
                    ]);
                }
                break;
        }
    }

    /**
     * Generate unique investor ID
     */
    protected function generateInvestorId(Investor $investor): string
    {
        $prefix = 'INV';
        $year = Carbon::now()->year;
        $sequence = str_pad($investor->id, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    /**
     * Auto-advance investor if eligible
     */
    public function autoAdvanceIfEligible(Investor $investor): bool
    {
        $currentStage = $investor->stage;
        
        // Define stage progression
        $progression = [
            'prospect' => 'eligibility_review',
            'eligibility_review' => 'ppm_issued',
            'ppm_issued' => 'kyc_in_progress',
            'kyc_in_progress' => 'subscription_signed',
            'subscription_signed' => 'approved',
            'approved' => 'funded',
            'funded' => 'active',
        ];

        // Check if there's a next stage
        if (!isset($progression[$currentStage])) {
            return false;
        }

        $nextStage = $progression[$currentStage];

        // Check if can move
        if ($this->canMoveToStage($investor, $nextStage)) {
            return $this->moveToStage($investor, $nextStage, 'Auto-advanced (criteria met)');
        }

        return false;
    }

    /**
     * Get missing requirements for stage
     */
    public function getMissingRequirements(Investor $investor, string $targetStage): array
    {
        $missing = [];

        if (!isset($this->stageRules[$targetStage])) {
            return $missing;
        }

        $rules = $this->stageRules[$targetStage];

        foreach ($rules as $field => $rule) {
            [$operator, $value] = $rule;
            $fieldValue = $investor->$field;

            $isMissing = false;

            switch ($operator) {
                case 'min':
                    $isMissing = $fieldValue < $value;
                    $message = ucfirst(str_replace('_', ' ', $field)) . " must be at least " . number_format($value);
                    break;
                case 'equals':
                    $isMissing = $fieldValue !== $value;
                    $message = ucfirst(str_replace('_', ' ', $field)) . " must be " . ($value ? 'true' : 'false');
                    break;
                case 'not_null':
                    $isMissing = is_null($fieldValue);
                    $message = ucfirst(str_replace('_', ' ', $field)) . " is required";
                    break;
                case 'greater_than':
                    $isMissing = $fieldValue <= $value;
                    $message = ucfirst(str_replace('_', ' ', $field)) . " must be greater than " . $value;
                    break;
            }

            if ($isMissing) {
                $missing[] = $message;
            }
        }

        return $missing;
    }
}