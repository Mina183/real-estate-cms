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
        Schema::create('partner_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uploaded_by'); // Admin ID
            $table->unsignedBigInteger('partner_id')->nullable(); // Null = all partners
            $table->string('title');
            $table->string('filename');
            $table->enum('status', ['pending_review', 'reviewed', 'pending_approval'])->default('pending_review');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_documents');
    }
};
