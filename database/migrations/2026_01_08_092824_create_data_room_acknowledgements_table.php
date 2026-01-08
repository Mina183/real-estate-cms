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
            
            $table->text('acknowledgement_text'); // Full text they agreed to
            $table->boolean('is_agreed')->default(false);
            $table->timestamp('agreed_at')->nullable();
            
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            
            // VERSION CONTROL (if terms change)
            $table->string('terms_version')->default('1.0');
            
            $table->timestamps();
            
            $table->unique(['user_id', 'acknowledgement_type', 'terms_version'], 'user_ack_version_unique');
            $table->index(['investor_id', 'acknowledgement_type']);
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
