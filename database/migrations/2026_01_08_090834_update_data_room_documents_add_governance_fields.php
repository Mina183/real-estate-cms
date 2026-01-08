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
            // GOVERNANCE (NEW FIELDS)
            // ============================================
            $table->foreignId('document_owner_id')->nullable()->after('folder_id')->constrained('users')->nullOnDelete();
            $table->date('approval_date')->nullable()->after('status');
            $table->foreignId('approved_by_user_id')->nullable()->after('approval_date')->constrained('users')->nullOnDelete();
            $table->date('effective_date')->nullable();
            $table->foreignId('supersedes_document_id')->nullable()->constrained('data_room_documents')->nullOnDelete();
            
            // ============================================
            // METADATA (NEW FIELDS)
            // ============================================
            $table->string('security_level')->nullable()->after('access_level');
            $table->json('tags')->nullable();
            $table->string('jurisdiction_tag')->nullable();
            $table->text('change_log')->nullable();
        });
        
        // Update status enum if it exists - add new values
        // Check if column exists first
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
            $table->dropForeign(['document_owner_id']);
            $table->dropForeign(['approved_by_user_id']);
            $table->dropForeign(['supersedes_document_id']);
            
            $table->dropColumn([
                'document_owner_id',
                'approval_date',
                'approved_by_user_id',
                'effective_date',
                'supersedes_document_id',
                'security_level',
                'tags',
                'jurisdiction_tag',
                'change_log'
            ]);
        });
    }
};
