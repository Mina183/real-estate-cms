<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
                'superadmin',
                'admin',
                'operations',
                'compliance_officer',
                'relationship_manager',
                'fund_manager',
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
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
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
    }
};