<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Renames investor stage slugs to align with the updated workflow.
 *
 * Old → New mapping:
 *   eligibility_review  → eligibility_confirmed
 *   ppm_issued          → portal_access_granted
 *   subscription_signed → portal_access_granted  (merged — subscription is now a gate, not a stage)
 *   approved            → kyc_completed
 *   active              → funded                 (merged with funded — single Funded/Active stage)
 *
 * Safe for fresh deployments (runs after the original CREATE TABLE migration)
 * and for existing databases (UPDATE rows before shrinking the ENUM).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Expand ENUM to include both old and new values ─────────────
        DB::statement("
            ALTER TABLE investors
            MODIFY COLUMN stage ENUM(
                'prospect',
                'eligibility_review',
                'eligibility_confirmed',
                'ppm_issued',
                'portal_access_granted',
                'kyc_in_progress',
                'subscription_signed',
                'kyc_completed',
                'approved',
                'funded',
                'active',
                'monitored'
            ) NOT NULL DEFAULT 'prospect'
        ");

        // ── Step 2: Migrate existing investor rows ──────────────────────────────
        DB::table('investors')->where('stage', 'eligibility_review')
            ->update(['stage' => 'eligibility_confirmed']);

        DB::table('investors')->whereIn('stage', ['ppm_issued', 'subscription_signed'])
            ->update(['stage' => 'portal_access_granted']);

        DB::table('investors')->where('stage', 'approved')
            ->update(['stage' => 'kyc_completed']);

        DB::table('investors')->where('stage', 'active')
            ->update(['stage' => 'funded']);

        // ── Step 3: Migrate stage transition history (plain strings, no ENUM) ───
        $transitions = [
            'eligibility_review'  => 'eligibility_confirmed',
            'ppm_issued'          => 'portal_access_granted',
            'subscription_signed' => 'portal_access_granted',
            'approved'            => 'kyc_completed',
            'active'              => 'funded',
        ];

        foreach ($transitions as $old => $new) {
            DB::table('investor_stage_transitions')
                ->where('from_stage', $old)->update(['from_stage' => $new]);
            DB::table('investor_stage_transitions')
                ->where('to_stage', $old)->update(['to_stage' => $new]);
        }

        // ── Step 4: Shrink ENUM to new values only ──────────────────────────────
        DB::statement("
            ALTER TABLE investors
            MODIFY COLUMN stage ENUM(
                'prospect',
                'eligibility_confirmed',
                'portal_access_granted',
                'kyc_in_progress',
                'kyc_completed',
                'funded',
                'monitored'
            ) NOT NULL DEFAULT 'prospect'
        ");
    }

    public function down(): void
    {
        // ── Step 1: Expand ENUM back to include both old and new values ─────────
        DB::statement("
            ALTER TABLE investors
            MODIFY COLUMN stage ENUM(
                'prospect',
                'eligibility_review',
                'eligibility_confirmed',
                'ppm_issued',
                'portal_access_granted',
                'kyc_in_progress',
                'subscription_signed',
                'kyc_completed',
                'approved',
                'funded',
                'active',
                'monitored'
            ) NOT NULL DEFAULT 'prospect'
        ");

        // ── Step 2: Revert investor rows ────────────────────────────────────────
        DB::table('investors')->where('stage', 'eligibility_confirmed')
            ->update(['stage' => 'eligibility_review']);

        DB::table('investors')->where('stage', 'portal_access_granted')
            ->update(['stage' => 'ppm_issued']);

        DB::table('investors')->where('stage', 'kyc_completed')
            ->update(['stage' => 'approved']);

        // Note: 'active' and 'funded' were merged; on rollback all go to 'funded'
        // which maps back correctly since funded existed in the old schema too.

        // ── Step 3: Revert transition history ───────────────────────────────────
        $transitions = [
            'eligibility_confirmed' => 'eligibility_review',
            'portal_access_granted' => 'ppm_issued',
            'kyc_completed'         => 'approved',
        ];

        foreach ($transitions as $new => $old) {
            DB::table('investor_stage_transitions')
                ->where('from_stage', $new)->update(['from_stage' => $old]);
            DB::table('investor_stage_transitions')
                ->where('to_stage', $new)->update(['to_stage' => $old]);
        }

        // ── Step 4: Shrink ENUM back to original values ──────────────────────────
        DB::statement("
            ALTER TABLE investors
            MODIFY COLUMN stage ENUM(
                'prospect',
                'eligibility_review',
                'ppm_issued',
                'kyc_in_progress',
                'subscription_signed',
                'approved',
                'funded',
                'active',
                'monitored'
            ) NOT NULL DEFAULT 'prospect'
        ");
    }
};
