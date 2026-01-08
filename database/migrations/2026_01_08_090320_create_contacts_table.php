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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            
            // ============================================
            // RELATIONSHIP
            // ============================================
            $table->foreignId('investor_id')->constrained('investors')->cascadeOnDelete();
            
            // ============================================
            // BASIC INFO
            // ============================================
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            
            // ============================================
            // ROLE & PERMISSIONS
            // ============================================
            $table->enum('role', [
                'primary_contact',
                'legal_counsel',
                'financial_officer',
                'authorized_signatory',
                'compliance_officer',
                'beneficial_owner',
                'other'
            ])->default('primary_contact');
            
            $table->boolean('is_primary')->default(false);
            $table->boolean('can_sign_documents')->default(false);
            $table->boolean('receives_capital_calls')->default(false);
            $table->boolean('receives_distributions')->default(false);
            $table->boolean('receives_reports')->default(false);
            
            // ============================================
            // PERSONAL INFO (For KYC)
            // ============================================
            $table->string('title')->nullable(); // Mr., Mrs., Dr., etc.
            $table->string('nationality')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('passport_number')->nullable(); // Encrypted
            $table->string('national_id')->nullable(); // Encrypted
            
            // ============================================
            // ADDRESS
            // ============================================
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            
            // ============================================
            // PREFERENCES
            // ============================================
            $table->string('preferred_language', 10)->default('en');
            $table->enum('preferred_contact_method', ['email', 'phone', 'both'])->default('email');
            
            // ============================================
            // PORTAL ACCESS
            // ============================================
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // ============================================
            // TIMESTAMPS
            // ============================================
            $table->softDeletes();
            $table->timestamps();
            
            // ============================================
            // INDEXES
            // ============================================
            $table->index('investor_id');
            $table->index('email');
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
