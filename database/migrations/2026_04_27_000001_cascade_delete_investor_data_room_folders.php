<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_room_folders', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->foreign('investor_id')
                ->references('id')
                ->on('investors')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('data_room_folders', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->foreign('investor_id')
                ->references('id')
                ->on('investors')
                ->nullOnDelete();
        });
    }
};
