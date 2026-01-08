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
        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            
            $table->string('fund_name');
            $table->string('fund_number')->unique()->nullable();
            $table->year('vintage_year')->nullable();
            $table->decimal('total_size', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            
            $table->enum('status', [
                'fundraising',
                'active',
                'closed'
            ])->default('active');
            
            $table->date('inception_date')->nullable();
            
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funds');
    }
};
