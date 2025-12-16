<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Folder structure for data room
        Schema::create('data_room_folders', function (Blueprint $table) {
            $table->id();
            $table->string('folder_number'); // e.g., "1.1", "7.x.1"
            $table->string('folder_name'); // e.g., "Executive Summary Deck"
            $table->unsignedBigInteger('parent_folder_id')->nullable(); // for subfolders
            $table->integer('order')->default(0);
            $table->text('description')->nullable();
            $table->enum('access_level', ['public', 'restricted', 'confidential', 'highly_confidential'])->default('restricted');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('parent_folder_id')->references('id')->on('data_room_folders')->onDelete('cascade');
        });

        // Documents stored in folders
        Schema::create('data_room_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained('data_room_folders')->onDelete('cascade');
            $table->string('document_name');
            $table->string('file_path'); // S3 path, encrypted
            $table->string('file_type'); // pdf, xlsx, docx
            $table->bigInteger('file_size'); // bytes
            $table->string('version')->default('1.0');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'pending_review', 'approved', 'archived'])->default('draft');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes(); // for document versioning
        });

        // Access permissions per folder/document
        Schema::create('data_room_permissions', function (Blueprint $table) {
            $table->id();
            $table->enum('resource_type', ['folder', 'document']);
            $table->unsignedBigInteger('resource_id'); // folder_id or document_id
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('user_role')->nullable(); // or assign by role
            $table->enum('permission_level', ['view', 'download', 'upload', 'manage'])->default('view');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Audit log for all data room activity
        Schema::create('data_room_activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('activity_type', ['view', 'download', 'upload', 'delete', 'share', 'permission_change']);
            $table->enum('resource_type', ['folder', 'document']);
            $table->unsignedBigInteger('resource_id');
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // additional context
            $table->timestamp('activity_at');
            $table->index(['user_id', 'activity_at']);
            $table->index(['resource_type', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_room_activity_log');
        Schema::dropIfExists('data_room_permissions');
        Schema::dropIfExists('data_room_documents');
        Schema::dropIfExists('data_room_folders');
    }
};
