<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_access_requests', function (Blueprint $table) {
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->timestamp('consent_recorded_at')->nullable()->after('user_agent');
            $table->string('consent_source')->nullable()->after('consent_recorded_at');
            $table->string('dp_notice_version')->nullable()->after('consent_source');
            $table->string('privacy_notice_version')->nullable()->after('dp_notice_version');
            $table->timestamp('first_accessed_at')->nullable()->after('privacy_notice_version');
            $table->unsignedInteger('download_count')->default(0)->after('first_accessed_at');
            $table->timestamp('last_downloaded_at')->nullable()->after('download_count');
            $table->string('last_download_ip', 45)->nullable()->after('last_downloaded_at');
            $table->text('last_download_user_agent')->nullable()->after('last_download_ip');
        });
    }

    public function down(): void
    {
        Schema::table('document_access_requests', function (Blueprint $table) {
            $table->dropColumn([
                'user_agent',
                'consent_recorded_at',
                'consent_source',
                'dp_notice_version',
                'privacy_notice_version',
                'first_accessed_at',
                'download_count',
                'last_downloaded_at',
                'last_download_ip',
                'last_download_user_agent',
            ]);
        });
    }
};
