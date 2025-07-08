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
        Schema::table('partner_documents', function (Blueprint $table) {
            $table->string('file_path')->after('filename');
            $table->timestamp('seen_by_partner_at')->nullable()->after('reviewed_at');
            $table->string('response_file_path')->nullable()->after('file_path');
            $table->timestamp('response_uploaded_at')->nullable()->after('seen_by_partner_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_documents', function (Blueprint $table) {
            //
        });
    }
};
