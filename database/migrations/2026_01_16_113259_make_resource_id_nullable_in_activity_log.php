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
        // Make resource_id and resource_type nullable
        DB::statement('ALTER TABLE data_room_activity_log MODIFY resource_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE data_room_activity_log MODIFY resource_type VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to NOT NULL (if needed)
        DB::statement('ALTER TABLE data_room_activity_log MODIFY resource_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE data_room_activity_log MODIFY resource_type VARCHAR(255) NOT NULL');
    }
};
