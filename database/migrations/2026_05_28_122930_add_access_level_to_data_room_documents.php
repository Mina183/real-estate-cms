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
        Schema::table('data_room_documents', function (Blueprint $table) {
            $table->enum('access_level', ['public', 'restricted', 'confidential', 'highly_confidential'])
                  ->default('restricted')
                  ->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('data_room_documents', function (Blueprint $table) {
            $table->dropColumn('access_level');
        });
    }
};
