<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE investors MODIFY COLUMN investor_type ENUM('individual','corporate','family_office','spv','fund','bank') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE investors MODIFY COLUMN investor_type ENUM('individual','corporate','family_office','spv','fund') NOT NULL");
    }
};
