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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->nullable()->constrained('funds')->onDelete('cascade');
            $table->string('distribution_number')->unique(); // e.g., "DIST-2026-001"
            $table->string('title'); // e.g., "Q4 2025 Profit Distribution"
            $table->text('description')->nullable();
            $table->enum('type', ['dividend', 'return_of_capital', 'profit_share', 'other'])->default('dividend');
            $table->decimal('total_amount', 15, 2); // Total being distributed
            $table->date('distribution_date'); // When distribution is made
            $table->date('record_date'); // Date for determining eligible investors
            $table->enum('status', ['draft', 'approved', 'processing', 'completed'])->default('draft');
            $table->decimal('total_distributed', 15, 2)->default(0); // Track distributions
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};

