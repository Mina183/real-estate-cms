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
        Schema::table('document_send_logs', function (Blueprint $table) {
            $table->string('document_name')->nullable()->after('document_id');
        });
    }

    public function down(): void
    {
        Schema::table('document_send_logs', function (Blueprint $table) {
            $table->dropColumn('document_name');
        });
    }
};
