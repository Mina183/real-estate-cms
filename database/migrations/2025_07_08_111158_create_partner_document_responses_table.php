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
        Schema::create('partner_document_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('users');
            $table->foreignId('document_id')->constrained('partner_documents');
            $table->string('response_file_path')->nullable();
            $table->timestamp('response_uploaded_at')->nullable();
            $table->enum('status', ['waiting_review', 'reviewed'])->default('waiting_review');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_document_responses');
    }
};
