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
        Schema::table('data_room_activity_log', function (Blueprint $table) {
            // ============================================
            // Add investor_id (NEW)
            // ============================================
            if (!Schema::hasColumn('data_room_activity_log', 'investor_id')) {
                $table->foreignId('investor_id')->nullable()->after('user_id')->constrained('investors')->cascadeOnDelete();
            }
            
            // ============================================
            // Add document_id and folder_id (ADDITIONAL to resource_type/resource_id)
            // We keep the old columns for compatibility, but add new ones for better relationships
            // ============================================
            if (!Schema::hasColumn('data_room_activity_log', 'document_id')) {
                $table->foreignId('document_id')->nullable()->after('investor_id')->constrained('data_room_documents')->cascadeOnDelete();
            }
            
            if (!Schema::hasColumn('data_room_activity_log', 'folder_id')) {
                $table->foreignId('folder_id')->nullable()->after('document_id')->constrained('data_room_folders')->cascadeOnDelete();
            }
            
            // Note: We're keeping resource_type and resource_id for backward compatibility
            // New code should use document_id/folder_id instead
            
            // ============================================
            // CONTEXT (NEW)
            // ============================================
            if (!Schema::hasColumn('data_room_activity_log', 'device_fingerprint')) {
                $table->string('device_fingerprint')->nullable();
            }
            
            // ============================================
            // COMPLIANCE (NEW)
            // ============================================
            if (!Schema::hasColumn('data_room_activity_log', 'is_suspicious')) {
                $table->boolean('is_suspicious')->default(false);
            }
            
            if (!Schema::hasColumn('data_room_activity_log', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
        
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE data_room_activity_log MODIFY COLUMN activity_type ENUM(
                'view',
                'download',
                'login',
                'logout',
                'print',
                'upload',
                'edit',
                'delete',
                'share',
                'permission_granted',
                'permission_revoked',
                'acknowledgement_signed',
                'failed_login',
                'failed_access'
            )");
        }
        // Add new indexes
        Schema::table('data_room_activity_log', function (Blueprint $table) {
            $table->index(['investor_id', 'activity_at']);
            $table->index('is_suspicious');
            $table->index('document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_room_activity_log', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->dropForeign(['document_id']);
            $table->dropForeign(['folder_id']);
            
            $table->dropColumn([
                'investor_id',
                'document_id',
                'folder_id',
                'device_fingerprint',
                'is_suspicious',
                'notes'
            ]);
        });
    }
};
