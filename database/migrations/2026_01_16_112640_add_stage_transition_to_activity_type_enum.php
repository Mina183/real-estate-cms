<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL enum modification requires raw SQL (skip on SQLite)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE data_room_activity_log MODIFY COLUMN activity_type ENUM(
                'view',
                'download',
                'upload',
                'delete',
                'share',
                'permission_change',
                'permission_granted',
                'permission_revoked',
                'stage_transition',
                'acknowledgement_signed',
                'failed_login',
                'failed_access'
            )");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE data_room_activity_log MODIFY COLUMN activity_type ENUM(
                'view',
                'download',
                'upload',
                'delete',
                'share',
                'permission_change'
            )");
        }
    }
};