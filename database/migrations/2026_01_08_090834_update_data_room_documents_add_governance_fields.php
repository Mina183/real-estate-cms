<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('data_room_documents', function (Blueprint $table) {
            // ============================================
            // GOVERNANCE - Only add if they don't exist
            // ============================================
            if (!Schema::hasColumn('data_room_documents', 'document_owner_id')) {
                $table->foreignId('document_owner_id')->nullable()->after('folder_id')->constrained('users')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('data_room_documents', 'approval_date')) {
                $table->date('approval_date')->nullable();
            }
            
            if (!Schema::hasColumn('data_room_documents', 'approved_by_user_id')) {
                $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('data_room_documents', 'effective_date')) {
                $table->date('effective_date')->nullable();
            }
            
            if (!Schema::hasColumn('data_room_documents', 'supersedes_document_id')) {
                $table->foreignId('supersedes_document_id')->nullable()->constrained('data_room_documents')->nullOnDelete();
            }
            
            // ============================================
            // METADATA - Only add if they don't exist
            // ============================================
            if (!Schema::hasColumn('data_room_documents', 'security_level')) {
                $table->string('security_level')->nullable();
            }
            
            if (!Schema::hasColumn('data_room_documents', 'tags')) {
                $table->json('tags')->nullable();
            }
            
            if (!Schema::hasColumn('data_room_documents', 'jurisdiction_tag')) {
                $table->string('jurisdiction_tag')->nullable();
            }
            
            if (!Schema::hasColumn('data_room_documents', 'change_log')) {
                $table->text('change_log')->nullable();
            }
        });
        
        // Update status enum if needed
        $hasStatusColumn = Schema::hasColumn('data_room_documents', 'status');
        
        if ($hasStatusColumn) {
            DB::statement("ALTER TABLE data_room_documents MODIFY COLUMN status ENUM(
                'draft',
                'under_review',
                'approved',
                'superseded',
                'archived',
                'pending_review'
            ) DEFAULT 'draft'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_room_documents', function (Blueprint $table) {
            if (Schema::hasColumn('data_room_documents', 'document_owner_id')) {
                $table->dropForeign(['document_owner_id']);
                $table->dropColumn('document_owner_id');
            }
            
            if (Schema::hasColumn('data_room_documents', 'approved_by_user_id')) {
                $table->dropForeign(['approved_by_user_id']);
                $table->dropColumn('approved_by_user_id');
            }
            
            if (Schema::hasColumn('data_room_documents', 'supersedes_document_id')) {
                $table->dropForeign(['supersedes_document_id']);
                $table->dropColumn('supersedes_document_id');
            }
            
            $table->dropColumn([
                'approval_date',
                'effective_date',
                'security_level',
                'tags',
                'jurisdiction_tag',
                'change_log'
            ]);
        });
    }
};