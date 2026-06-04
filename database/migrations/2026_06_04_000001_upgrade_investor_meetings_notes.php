<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investor_meetings', function (Blueprint $table) {
            $table->longText('outcome')->nullable()->change();
            $table->string('transcript_path')->nullable()->after('outcome');
            $table->string('transcript_name')->nullable()->after('transcript_path');
        });
    }

    public function down(): void
    {
        Schema::table('investor_meetings', function (Blueprint $table) {
            $table->string('outcome', 1000)->nullable()->change();
            $table->dropColumn(['transcript_path', 'transcript_name']);
        });
    }
};
