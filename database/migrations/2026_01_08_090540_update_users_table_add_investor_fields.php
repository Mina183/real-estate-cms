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
 public function up()
    {
        // Skip MODIFY on SQLite, only runs on MySQL
        if (DB::connection()->getDriverName() !== 'sqlite') {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->dropColumn('investor_id');
        });
        
        // Restore old roles
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'superadmin',
            'admin',
            'channel_partner'
        )");
    }
};
