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
        Schema::create('document_send_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained('investors')->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('data_room_documents')->nullOnDelete();
            $table->string('template');
            $table->foreignId('sent_by_user_id')->constrained('users');
            $table->string('sent_to_email');
            $table->string('document_version')->nullable();
            $table->timestamp('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_send_logs');
    }
};
