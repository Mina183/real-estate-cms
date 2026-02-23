<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds only truly missing fields that don't already exist in investors table.
     * Checked against existing schema on 2026-02-23.
     */
    public function up(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            // Stage 2: Eligibility Review
            $table->text('investor_experience')->nullable()->after('risk_profile');
            
            // Stage 5: Subscription Docs
            $table->boolean('legal_review_complete')->default(false)->after('side_letter_terms');
            
            // Stage 6: Approval & Admission
            $table->boolean('board_approval_required')->default(false)->after('approving_authority');
            $table->date('board_approval_date')->nullable()->after('board_approval_required');
            
            // Stage 8: Investor Activated
            $table->decimal('units_allotted', 15, 4)->nullable()->after('investor_id_number');
            $table->date('welcome_letter_sent_date')->nullable()->after('reporting_access_granted');
            $table->boolean('investor_register_updated')->default(false)->after('welcome_letter_sent_date');
            
            // CRM Non-Negotiables
            $table->text('next_action')->nullable()->after('notes');
            $table->date('next_action_due_date')->nullable()->after('next_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn([
                'investor_experience',
                'legal_review_complete',
                'board_approval_required',
                'board_approval_date',
                'units_allotted',
                'welcome_letter_sent_date',
                'investor_register_updated',
                'next_action',
                'next_action_due_date',
            ]);
        });
    }
};