<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            // Eligibility Confirmed gate: DIFC Data Protection consent
            $table->boolean('difc_dp_consent')->default(false)->after('agreed_confidentiality_at');
            $table->timestamp('difc_dp_consent_at')->nullable()->after('difc_dp_consent');

            // KYC Completed/Approved gate: Commitment Letter signed
            $table->boolean('commitment_letter_signed')->default(false)->after('kyc_completed_date');
            $table->timestamp('commitment_letter_signed_at')->nullable()->after('commitment_letter_signed');
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn([
                'difc_dp_consent',
                'difc_dp_consent_at',
                'commitment_letter_signed',
                'commitment_letter_signed_at',
            ]);
        });
    }
};
