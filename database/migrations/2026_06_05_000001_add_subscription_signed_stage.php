<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE investors MODIFY COLUMN stage ENUM(
            'prospect',
            'eligibility_confirmed',
            'portal_access_granted',
            'subscription_signed',
            'kyc_in_progress',
            'kyc_completed',
            'funded',
            'monitored'
        ) NOT NULL DEFAULT 'prospect'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE investors MODIFY COLUMN stage ENUM(
            'prospect',
            'eligibility_confirmed',
            'portal_access_granted',
            'kyc_in_progress',
            'kyc_completed',
            'funded',
            'monitored'
        ) NOT NULL DEFAULT 'prospect'");
    }
};
