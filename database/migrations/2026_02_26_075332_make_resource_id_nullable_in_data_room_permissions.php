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
        $table->unsignedBigInteger('resource_id')->nullable()->change();
        $table->enum('resource_type', ['folder', 'document'])->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('data_room_permissions', function (Blueprint $table) {
        $table->unsignedBigInteger('resource_id')->nullable(false)->change();
        $table->enum('resource_type', ['folder', 'document'])->nullable(false)->change();
    });
}
};
