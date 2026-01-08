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
        // Check if table already exists from previous failed migration
        if (Schema::hasTable('data_room_acknowledgements')) {
            return; // Skip if already exists
        }
        
        Schema::create('data_room_acknowledgements', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('investor_id')->nullable()->constrained('investors')->cascadeOnDelete();
            
            $table->enum('acknowledgement_type', [
                'professional_client_confirmation',
                'confidentiality_agreement',
                'ppm_confidentiality',
                'risk_warnings',
                'data_room_terms',
                'non_distribution_agreement'
            ]);
            
            $table->text('acknowledgement_text');
            $table->boolean('is_agreed')->default(false);
            $table->timestamp('agreed_at')->nullable();
            
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            
            $table->string('terms_version')->default('1.0');
            
            $table->timestamps();
            
            // Shorter index names
            $table->unique(['user_id', 'acknowledgement_type', 'terms_version'], 'dr_ack_user_type_ver_unq');
            $table->index(['investor_id', 'acknowledgement_type'], 'dr_ack_inv_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_room_acknowledgements');
    }
};