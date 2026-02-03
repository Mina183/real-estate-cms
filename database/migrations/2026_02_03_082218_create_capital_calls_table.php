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
        Schema::create('capital_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->nullable()->constrained('funds')->onDelete('cascade');
            $table->string('call_number')->unique(); // e.g., "CC-2026-001"
            $table->string('title'); // e.g., "Q1 2026 Capital Call"
            $table->text('description')->nullable();
            $table->decimal('total_amount', 15, 2); // Total amount being called
            $table->date('call_date'); // When the call was issued
            $table->date('due_date'); // Payment deadline
            $table->enum('status', ['draft', 'issued', 'partially_paid', 'fully_paid', 'overdue'])->default('draft');
            $table->decimal('total_received', 15, 2)->default(0); // Track payments
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
        Schema::dropIfExists('capital_calls');
    }
};

