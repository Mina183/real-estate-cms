<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investor_meetings', function (Blueprint $table) {
            $table->string('title', 200)->nullable()->after('investor_id');
            $table->time('meeting_time')->nullable()->after('meeting_date');
            $table->string('meeting_timezone', 64)->nullable()->after('meeting_time');
        });
    }

    public function down(): void
    {
        Schema::table('investor_meetings', function (Blueprint $table) {
            $table->dropColumn(['title', 'meeting_time', 'meeting_timezone']);
        });
    }
};
