<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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
            'failed_access',
            'compliance_gate_accepted'
        )");
    }

    public function down(): void
    {
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
};