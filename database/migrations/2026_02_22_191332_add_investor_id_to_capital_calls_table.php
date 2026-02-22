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
        Schema::table('capital_calls', function (Blueprint $table) {
            // Add investor_id to link capital calls to specific investors
            $table->foreignId('investor_id')
                ->nullable()
                ->after('id')
                ->constrained('investors')
                ->cascadeOnDelete();
            
            // Index for efficient queries
            $table->index(['investor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capital_calls', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->dropIndex(['investor_id', 'status']);
            $table->dropColumn('investor_id');
        });
    }
};
