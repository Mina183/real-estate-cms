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
            Schema::table('data_room_permissions', function (Blueprint $table) {
            // ============================================
            // Add investor_id (if not exists)
            // ============================================
            if (!Schema::hasColumn('data_room_permissions', 'investor_id')) {
                $table->foreignId('investor_id')->nullable()->after('user_id')->constrained('investors')->cascadeOnDelete();
            }
            
            // ============================================
            // Add folder_id (if not exists) - renamed from resource_id
            // ============================================
            if (!Schema::hasColumn('data_room_permissions', 'folder_id')) {
                $table->foreignId('folder_id')->nullable()->after('investor_id')->constrained('data_room_folders')->cascadeOnDelete();
            }
            
            // ============================================
            // CAPABILITIES (NEW)
            // ============================================
            $table->boolean('can_view')->default(true);
            $table->boolean('can_download')->default(true);
            $table->boolean('can_print')->default(false);
            $table->boolean('can_upload')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            
            // ============================================
            // GOVERNANCE (NEW)
            // ============================================
            $table->foreignId('granted_by_user_id')->nullable()->constrained('users');
            $table->timestamp('granted_at')->nullable();
            $table->text('access_reason')->nullable();
            
            // ============================================
            // RESTRICTIONS (NEW)
            // ============================================
            $table->json('ip_whitelist')->nullable();
            $table->json('device_restrictions')->nullable();
            $table->boolean('requires_2fa')->default(false);
            $table->boolean('requires_watermark')->default(false);
            
            // ============================================
            // STATUS (NEW)
            // ============================================
            $table->boolean('is_active')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by_user_id')->nullable()->constrained('users');
        });
        
        // Add new indexes
        Schema::table('data_room_permissions', function (Blueprint $table) {
            $table->index(['investor_id', 'is_active']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('data_room_permissions', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->dropForeign(['folder_id']);
            $table->dropForeign(['granted_by_user_id']);
            $table->dropForeign(['revoked_by_user_id']);
            
            $table->dropColumn([
                'investor_id',
                'folder_id',
                'can_view',
                'can_download',
                'can_print',
                'can_upload',
                'can_edit',
                'can_delete',
                'granted_by_user_id',
                'granted_at',
                'access_reason',
                'ip_whitelist',
                'device_restrictions',
                'requires_2fa',
                'requires_watermark',
                'is_active',
                'revoked_at',
                'revoked_by_user_id'
            ]);
        });
    }
};
