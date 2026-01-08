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
        // Archive old CRM tables by renaming them
        $oldTables = [
            'clients',
            'client_communications',
            'client_documents',
            'meetings',
            'meeting_users',
            'partner_documents',
            'partner_document_responses',
            'lead_sources',
        ];
        
        foreach ($oldTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::rename($table, $table . '_old');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old tables
        $oldTables = [
            'clients',
            'client_communications',
            'client_documents',
            'meetings',
            'meeting_users',
            'partner_documents',
            'partner_document_responses',
            'lead_sources',
        ];
        
        foreach ($oldTables as $table) {
            if (Schema::hasTable($table . '_old')) {
                Schema::rename($table . '_old', $table);
            }
        }
    }
};
