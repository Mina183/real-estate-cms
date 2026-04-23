<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE investors
            MODIFY COLUMN data_room_access_level
            ENUM('none','prospect','qualified','subscribed','internal','external','viewer')
            NOT NULL DEFAULT 'none'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE investors
            MODIFY COLUMN data_room_access_level
            ENUM('none','prospect','qualified','subscribed','internal','external')
            NOT NULL DEFAULT 'none'
        ");
    }
};
