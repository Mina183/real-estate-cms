<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove investor_id from capital_calls
        if (Schema::hasColumn('capital_calls', 'investor_id')) {
            Schema::table('capital_calls', function (Blueprint $table) {
                $table->dropForeign(['investor_id']);
                $table->dropColumn('investor_id');
            });
        }

        // Remove investor_id from distributions
        if (Schema::hasColumn('distributions', 'investor_id')) {
            Schema::table('distributions', function (Blueprint $table) {
                $table->dropForeign(['investor_id']);
                // Drop composite index if exists
                try {
                    $table->dropIndex(['investor_id', 'status']);
                } catch (\Exception $e) {
                    // Index may not exist, ignore
                }
                $table->dropColumn('investor_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('capital_calls', function (Blueprint $table) {
            $table->foreignId('investor_id')->nullable()->after('id')->constrained('investors')->cascadeOnDelete();
        });

        Schema::table('distributions', function (Blueprint $table) {
            $table->foreignId('investor_id')->nullable()->after('id')->constrained('investors')->cascadeOnDelete();
            $table->index(['investor_id', 'status']);
        });
    }
};
