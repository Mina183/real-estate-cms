<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_drafts', function (Blueprint $table) {
            $table->unsignedBigInteger('investor_id')->nullable()->change();
            $table->boolean('is_bulk')->default(false)->after('investor_id');
            $table->string('bulk_recipient_type', 20)->nullable()->after('is_bulk');
            $table->string('bulk_recipient_stage', 50)->nullable()->after('bulk_recipient_type');
            $table->unsignedBigInteger('bulk_assigned_to_user_id')->nullable()->after('bulk_recipient_stage');
            $table->json('bulk_recipient_ids')->nullable()->after('bulk_assigned_to_user_id');
            $table->unsignedInteger('bulk_recipient_count')->default(0)->after('bulk_recipient_ids');
        });
    }

    public function down(): void
    {
        Schema::table('email_drafts', function (Blueprint $table) {
            $table->dropColumn([
                'is_bulk', 'bulk_recipient_type', 'bulk_recipient_stage',
                'bulk_assigned_to_user_id', 'bulk_recipient_ids', 'bulk_recipient_count',
            ]);
            $table->unsignedBigInteger('investor_id')->nullable(false)->change();
        });
    }
};
