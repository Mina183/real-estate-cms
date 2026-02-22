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
        Schema::table('data_room_documents', function (Blueprint $table) {
            // Add investor_id for investor-specific documents
            // NULL = available to all investors (general documents)
            // NOT NULL = only visible to specific investor
            $table->foreignId('investor_id')
                ->nullable()
                ->after('folder_id')
                ->constrained('investors')
                ->nullOnDelete();
            
            // Index for efficient queries
            $table->index(['folder_id', 'investor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_room_documents', function (Blueprint $table) {
            $table->dropForeign(['investor_id']);
            $table->dropIndex(['folder_id', 'investor_id']);
            $table->dropColumn('investor_id');
        });
    }
};
