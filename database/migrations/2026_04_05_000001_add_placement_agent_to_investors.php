<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->string('placement_agent_name')->nullable()->after('referral_source');
            $table->string('placement_agent_email')->nullable()->after('placement_agent_name');
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn(['placement_agent_name', 'placement_agent_email']);
        });
    }
};
