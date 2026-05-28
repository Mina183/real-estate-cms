<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // All existing documents default to public
        DB::table('data_room_documents')->update(['access_level' => 'public']);

        // These two are restricted-selective
        DB::table('data_room_documents')
            ->where('document_name', 'LIKE', '%Director Checklist%Restricted%')
            ->update(['access_level' => 'restricted']);

        DB::table('data_room_documents')
            ->where('document_name', 'Triton Fund Management Agreement Executed')
            ->update(['access_level' => 'restricted']);

        // Change column default to public
        DB::statement("ALTER TABLE data_room_documents MODIFY COLUMN access_level ENUM('public','restricted','confidential','highly_confidential') NOT NULL DEFAULT 'public'");
    }

    public function down(): void
    {
        DB::table('data_room_documents')->update(['access_level' => 'restricted']);
        DB::statement("ALTER TABLE data_room_documents MODIFY COLUMN access_level ENUM('public','restricted','confidential','highly_confidential') NOT NULL DEFAULT 'restricted'");
    }
};
