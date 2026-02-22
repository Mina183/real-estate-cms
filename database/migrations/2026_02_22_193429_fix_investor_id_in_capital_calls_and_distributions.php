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
    // Fix capital_calls - make nullable if exists, create if not
    if (Schema::hasColumn('capital_calls', 'investor_id')) {
        DB::statement('ALTER TABLE capital_calls MODIFY COLUMN investor_id bigint unsigned NULL');
    } else {
        Schema::table('capital_calls', function (Blueprint $table) {
            $table->foreignId('investor_id')->nullable()->after('id')->constrained('investors')->cascadeOnDelete();
        });
    }

    // Add to distributions (doesn't exist yet)
    Schema::table('distributions', function (Blueprint $table) {
        $table->foreignId('investor_id')->nullable()->after('id')->constrained('investors')->cascadeOnDelete();
        $table->index(['investor_id', 'status']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributions', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->dropIndex(['investor_id', 'status']);
            $table->dropColumn('investor_id');
        });
        
        // Don't touch capital_calls in down - already existed
        }
};
