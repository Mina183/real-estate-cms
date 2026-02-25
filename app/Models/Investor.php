<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Basic info
        'investor_type',
        'organization_name',
        'legal_entity_name',
        'jurisdiction',
        
        // Workflow
        'stage',
        'status',
        'lifecycle_status',
        
        // Relationship
        'source_of_introduction',
        'referral_source',
        'assigned_to_user_id',
        
        // Financial
        'target_commitment_amount',
        'final_commitment_amount',
        'funded_amount',
        'currency',
        'fund_id',
        
        // Stage 2: Eligibility
        'is_professional_client',
        'risk_profile',
        'sanctions_check_passed',
        'sanctions_checked_at',
        
        // Stage 3: PPM
        'ppm_issued_date',
        'ppm_version',
        'ppm_acknowledged_date',
        'data_room_access_granted',
        
        // Stage 4: KYC
        'kyc_status',
        'kyc_risk_rating',
        'enhanced_due_diligence_required',
        'kyc_completed_date',
        'kyc_expires_at',
        'kyc_reviewed_by',
        'is_pep',
        'fatca_compliant',
        'crs_compliant',
        
        // Stage 5: Subscription
        'subscription_signed_date',
        'share_class',
        'side_letter_exists',
        'side_letter_terms',
        
        // Stage 6: Approval
        'approved_date',
        'approved_by_user_id',
        'approving_authority',
        'admission_notice_issued_date',
        
        // Stage 7: Funding
        'funding_date',
        'bank_account_verified',
        'bank_verified_date',
        
        // Stage 8: Active
        'activated_date',
        'investor_id_number',
        'reporting_access_granted',
        'quarterly_reporting_included',
        
        // Stage 9: Monitoring
        'last_kyc_refresh_date',
        'next_kyc_refresh_due',
        'last_sanctions_rescreen_date',
        
        // Data Room
        'data_room_access_level',
        'data_room_access_granted_at',
        'data_room_access_expires_at',
        
        // Compliance Gates
        'confirmed_professional_client',
        'confirmed_professional_client_at',
        'agreed_confidentiality',
        'agreed_confidentiality_at',
        'acknowledged_ppm_confidential',
        'acknowledged_ppm_confidential_at',
        'acknowledged_risk_warnings',
        'acknowledged_risk_warnings_at',
        
        // Analytics
        'data_room_last_login',
        'data_room_login_count',
        'data_room_documents_viewed',
        
        // Sensitive (encrypted)
        'tax_id',
        'passport_number',
        'national_id',
        
        // Metadata
        'notes',
        'metadata',
        'created_by_user_id',

        // NEW FIELDS
        'investor_experience',
        'legal_review_complete',
        'board_approval_required',
        'board_approval_date',
        'units_allotted',
        'welcome_letter_sent_date',
        'investor_register_updated',
        'next_action',
        'next_action_due_date',
    ];

    protected $casts = [
        'target_commitment_amount' => 'decimal:2',
        'final_commitment_amount' => 'decimal:2',
        'funded_amount' => 'decimal:2',
        'is_professional_client' => 'boolean',
        'sanctions_check_passed' => 'boolean',
        'sanctions_checked_at' => 'date',
        'ppm_issued_date' => 'date',
        'ppm_acknowledged_date' => 'date',
        'data_room_access_granted' => 'boolean',
        'kyc_completed_date' => 'date',
        'kyc_expires_at' => 'date',
        'enhanced_due_diligence_required' => 'boolean',
        'is_pep' => 'boolean',
        'fatca_compliant' => 'boolean',
        'crs_compliant' => 'boolean',
        'subscription_signed_date' => 'date',
        'side_letter_exists' => 'boolean',
        'approved_date' => 'date',
        'admission_notice_issued_date' => 'date',
        'funding_date' => 'date',
        'bank_account_verified' => 'boolean',
        'bank_verified_date' => 'date',
        'activated_date' => 'date',
        'reporting_access_granted' => 'boolean',
        'quarterly_reporting_included' => 'boolean',
        'last_kyc_refresh_date' => 'date',
        'next_kyc_refresh_due' => 'date',
        'last_sanctions_rescreen_date' => 'date',
        'data_room_access_granted_at' => 'datetime',
        'data_room_access_expires_at' => 'datetime',
        'confirmed_professional_client' => 'boolean',
        'confirmed_professional_client_at' => 'datetime',
        'agreed_confidentiality' => 'boolean',
        'agreed_confidentiality_at' => 'datetime',
        'acknowledged_ppm_confidential' => 'boolean',
        'acknowledged_ppm_confidential_at' => 'datetime',
        'acknowledged_risk_warnings' => 'boolean',
        'acknowledged_risk_warnings_at' => 'datetime',
        'data_room_last_login' => 'datetime',
        'data_room_login_count' => 'integer',
        'data_room_documents_viewed' => 'integer',
        'metadata' => 'array',
        'legal_review_complete' => 'boolean',
        'board_approval_required' => 'boolean',
        'investor_register_updated' => 'boolean',
        'board_approval_date' => 'date',
        'welcome_letter_sent_date' => 'date',
        'next_action_due_date' => 'date',
        'units_allotted' => 'decimal:4',
        'tax_id' => 'encrypted',
        'passport_number' => 'encrypted',
        'national_id' => 'encrypted',
    ];

    // Relationships
    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function commitments()
    {
        return $this->hasMany(Commitment::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function stageTransitions()
    {
        return $this->hasMany(InvestorStageTransition::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function capitalCallPayments()
    {
        return $this->hasMany(PaymentTransaction::class)
                    ->where('transaction_type', 'capital_call');
    }

    public function distributionPayments()
    {
        return $this->hasMany(PaymentTransaction::class)
                    ->where('transaction_type', 'distribution');
    }

    public function kycReviewedBy()
    {
        return $this->belongsTo(User::class, 'kyc_reviewed_by');
    }

    public function user()
{
    return $this->hasOne(InvestorUser::class);
}
}
