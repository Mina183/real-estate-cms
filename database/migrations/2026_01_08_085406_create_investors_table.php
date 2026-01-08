<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            
            // ============================================
            // BASIC INFO (Stage 1)
            // ============================================
            $table->enum('investor_type', [
                'individual', 
                'corporate', 
                'family_office', 
                'spv', 
                'fund'
            ]);
            $table->string('organization_name')->nullable();
            $table->string('legal_entity_name')->nullable();
            $table->string('jurisdiction', 100);
            
            // ============================================
            // WORKFLOW (All Stages)
            // ============================================
            $table->enum('stage', [
                'prospect',              // Stage 1
                'eligibility_review',    // Stage 2
                'ppm_issued',            // Stage 3
                'kyc_in_progress',       // Stage 4
                'subscription_signed',   // Stage 5
                'approved',              // Stage 6
                'funded',                // Stage 7
                'active',                // Stage 8
                'monitored'              // Stage 9
            ])->default('prospect');
            
            $table->enum('status', [
                'pending',
                'in_review',
                'qualified',
                'action_required',
                'on_hold',
                'rejected'
            ])->default('pending');
            
            $table->enum('lifecycle_status', [
                'active',
                'inactive',
                'archived'
            ])->default('active');
            
            // ============================================
            // RELATIONSHIP
            // ============================================
            $table->enum('source_of_introduction', [
                'direct',
                'advisor',
                'placement_agent',
                'referral',
                'event',
                'other'
            ])->nullable();
            $table->string('referral_source')->nullable();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // ============================================
            // FINANCIAL (Stage 1, 5, 7)
            // ============================================
            $table->decimal('target_commitment_amount', 15, 2)->nullable();
            $table->decimal('final_commitment_amount', 15, 2)->nullable();
            $table->decimal('funded_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->foreignId('fund_id')->nullable()->constrained('funds')->nullOnDelete();
            
            // ============================================
            // STAGE 2: Eligibility
            // ============================================
            $table->boolean('is_professional_client')->default(false);
            $table->enum('risk_profile', ['low', 'medium', 'high'])->nullable();
            $table->boolean('sanctions_check_passed')->default(false);
            $table->date('sanctions_checked_at')->nullable();
            
            // ============================================
            // STAGE 3: PPM
            // ============================================
            $table->date('ppm_issued_date')->nullable();
            $table->string('ppm_version')->nullable();
            $table->date('ppm_acknowledged_date')->nullable();
            $table->boolean('data_room_access_granted')->default(false);
            
            // ============================================
            // STAGE 4: KYC/AML
            // ============================================
            $table->enum('kyc_status', [
                'not_started',
                'in_progress',
                'submitted',
                'under_review',
                'complete',
                'rejected',
                'expired'
            ])->default('not_started');
            $table->enum('kyc_risk_rating', ['low', 'medium', 'high'])->nullable();
            $table->boolean('enhanced_due_diligence_required')->default(false);
            $table->date('kyc_completed_date')->nullable();
            $table->date('kyc_expires_at')->nullable();
            $table->foreignId('kyc_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->boolean('is_pep')->default(false);
            $table->boolean('fatca_compliant')->default(false);
            $table->boolean('crs_compliant')->default(false);
            
            // ============================================
            // STAGE 5: Subscription
            // ============================================
            $table->date('subscription_signed_date')->nullable();
            $table->string('share_class')->nullable();
            $table->boolean('side_letter_exists')->default(false);
            $table->text('side_letter_terms')->nullable();
            
            // ============================================
            // STAGE 6: Approval
            // ============================================
            $table->date('approved_date')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approving_authority')->nullable();
            $table->date('admission_notice_issued_date')->nullable();
            
            // ============================================
            // STAGE 7: Funding
            // ============================================
            $table->date('funding_date')->nullable();
            $table->boolean('bank_account_verified')->default(false);
            $table->date('bank_verified_date')->nullable();
            
            // ============================================
            // STAGE 8: Active
            // ============================================
            $table->date('activated_date')->nullable();
            $table->string('investor_id_number')->nullable()->unique();
            $table->boolean('reporting_access_granted')->default(false);
            $table->boolean('quarterly_reporting_included')->default(true);
            
            // ============================================
            // STAGE 9: Monitoring
            // ============================================
            $table->date('last_kyc_refresh_date')->nullable();
            $table->date('next_kyc_refresh_due')->nullable();
            $table->date('last_sanctions_rescreen_date')->nullable();
            
            // ============================================
            // DATA ROOM ACCESS
            // ============================================
            $table->enum('data_room_access_level', [
                'none',
                'prospect',
                'qualified',
                'subscribed',
                'internal',
                'external'
            ])->default('none');
            $table->timestamp('data_room_access_granted_at')->nullable();
            $table->timestamp('data_room_access_expires_at')->nullable();
            
            // ============================================
            // COMPLIANCE GATES
            // ============================================
            $table->boolean('confirmed_professional_client')->default(false);
            $table->timestamp('confirmed_professional_client_at')->nullable();
            $table->boolean('agreed_confidentiality')->default(false);
            $table->timestamp('agreed_confidentiality_at')->nullable();
            $table->boolean('acknowledged_ppm_confidential')->default(false);
            $table->timestamp('acknowledged_ppm_confidential_at')->nullable();
            $table->boolean('acknowledged_risk_warnings')->default(false);
            $table->timestamp('acknowledged_risk_warnings_at')->nullable();
            
            // ============================================
            // DATA ROOM ANALYTICS
            // ============================================
            $table->timestamp('data_room_last_login')->nullable();
            $table->integer('data_room_login_count')->default(0);
            $table->integer('data_room_documents_viewed')->default(0);
            
            // ============================================
            // SENSITIVE DATA (Encrypted)
            // ============================================
            $table->string('tax_id')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('national_id')->nullable();
            
            // ============================================
            // METADATA
            // ============================================
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // ============================================
            // TIMESTAMPS
            // ============================================
            $table->softDeletes();
            $table->timestamps();
            
            // ============================================
            // INDEXES
            // ============================================
            $table->index(['stage', 'status']);
            $table->index('lifecycle_status');
            $table->index('kyc_status');
            $table->index('jurisdiction');
            $table->index('assigned_to_user_id');
            $table->index('data_room_access_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
