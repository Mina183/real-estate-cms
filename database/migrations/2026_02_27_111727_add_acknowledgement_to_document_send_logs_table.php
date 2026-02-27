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
            $table->string('acknowledgement_token')->nullable()->unique()->after('sent_at');
            $table->timestamp('acknowledged_at')->nullable()->after('acknowledgement_token');
            $table->string('email_subject')->nullable()->after('template');
            $table->boolean('requires_acknowledgement')->default(false)->after('acknowledged_at');
        });
    }

    public function down(): void
    {
        Schema::table('document_send_logs', function (Blueprint $table) {
            $table->dropColumn(['acknowledgement_token', 'acknowledged_at', 'email_subject', 'requires_acknowledgement']);
        });
    }
};
