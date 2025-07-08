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
    DB::statement("ALTER TABLE partner_document_responses MODIFY COLUMN status ENUM(
        'waiting_partner_action',
        'waiting_admin_approval',
        'review_only',
        'acknowledged',
        'complete'
    ) DEFAULT 'waiting_partner_action'");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE partner_document_responses MODIFY COLUMN status ENUM(
        'waiting_review',
        'reviewed'
    ) DEFAULT 'waiting_review'");
    }
};
