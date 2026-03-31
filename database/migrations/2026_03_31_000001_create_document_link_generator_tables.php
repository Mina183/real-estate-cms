<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('document_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_package_id')->constrained('document_packages')->cascadeOnDelete();
            $table->foreignId('data_room_document_id')->constrained('data_room_documents')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['document_package_id', 'data_room_document_id'], 'pkg_items_unique');
        });

        Schema::create('document_access_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_package_id')->constrained('document_packages')->cascadeOnDelete();
            $table->foreignId('investor_id')->nullable()->constrained('investors')->nullOnDelete();
            $table->string('token', 64)->unique();
            $table->string('label')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamps();
            $table->index('token');
        });

        Schema::create('document_access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_access_link_id')->constrained('document_access_links')->cascadeOnDelete();
            $table->string('requester_name');
            $table->string('requester_email');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['document_access_link_id', 'requester_email'], 'dar_link_email_idx');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_access_requests');
        Schema::dropIfExists('document_access_links');
        Schema::dropIfExists('document_package_items');
        Schema::dropIfExists('document_packages');
    }
};
