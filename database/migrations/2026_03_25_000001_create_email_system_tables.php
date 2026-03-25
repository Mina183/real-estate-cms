<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // display name e.g. "John Smith — Director"
            $table->text('signature_html'); // full signature block
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('email_on_behalf', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "John Smith"
            $table->string('title')->nullable(); // e.g. "Managing Director"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('email_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained('investors')->cascadeOnDelete();
            $table->string('template_key');
            $table->foreignId('on_behalf_of_id')->nullable()->constrained('email_on_behalf')->nullOnDelete();
            $table->foreignId('signature_id')->nullable()->constrained('email_signatures')->nullOnDelete();
            $table->string('subject');
            $table->text('body');
            $table->json('document_ids')->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'sent'])->default('draft');
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('email_body_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject_suggestion')->nullable();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_drafts');
        Schema::dropIfExists('email_on_behalf');
        Schema::dropIfExists('email_signatures');
        Schema::dropIfExists('email_body_templates');
    }
};