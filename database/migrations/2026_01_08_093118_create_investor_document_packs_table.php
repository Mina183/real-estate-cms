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
        Schema::create('investor_document_packs', function (Blueprint $table) {
            $table->id();
            
            $table->string('pack_name'); // "Prospect Pack", "DD Pack", etc.
            $table->text('description')->nullable();
            
            $table->enum('pack_type', [
                'prospect',
                'due_diligence',
                'subscribed'
            ]);
            
            $table->enum('required_access_level', [
                'prospect',
                'qualified',
                'subscribed'
            ]);
            
            $table->json('included_document_ids'); // Array of document IDs
            $table->json('included_folder_ids')->nullable(); // Or entire folders
            
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            
            $table->timestamps();
            
            $table->index('pack_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_document_packs');
    }
};
