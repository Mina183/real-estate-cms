<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing channel_partner users to operations
        DB::table('users')
            ->where('role', 'channel_partner')
            ->update(['role' => 'admin']); // Temporarily set to admin (valid value)
        
        // Update the ENUM to include new roles and remove channel_partner
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'superadmin',
            'admin',
            'operations',
            'compliance_officer',
            'relationship_manager',
            'data_room_administrator',
            'document_owner',
            'investor_prospect',
            'investor_qualified',
            'investor_subscribed',
            'internal_director',
            'external_counsel',
            'auditor'
        ) DEFAULT 'investor_prospect'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old ENUM (for rollback)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'superadmin',
            'admin',
            'channel_partner',
            'compliance_officer',
            'relationship_manager',
            'data_room_administrator',
            'document_owner',
            'investor_prospect',
            'investor_qualified',
            'investor_subscribed',
            'internal_director',
            'external_counsel'
        )");
    }
};
