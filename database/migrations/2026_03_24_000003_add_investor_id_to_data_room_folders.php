<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_room_folders', function (Blueprint $table) {
            $table->foreignId('investor_id')
                ->nullable()
                ->after('parent_folder_id')
                ->constrained('investors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('data_room_folders', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->dropColumn('investor_id');
        });
    }
};