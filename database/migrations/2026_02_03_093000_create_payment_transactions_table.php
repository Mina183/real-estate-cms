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

        Schema::dropIfExists('payment_transactions');
        
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transactionable_id');
            $table->string('transactionable_type');
            $table->index(['transactionable_type', 'transactionable_id'], 'payment_trans_morph_index'); // Kraći naziv!
            $table->foreignId('investor_id')->constrained('investors')->onDelete('cascade');
            $table->string('transaction_type'); // 'capital_call' or 'distribution'
            $table->decimal('amount', 15, 2);
            $table->decimal('commitment_percentage', 5, 2)->nullable(); // % of investor's commitment
            $table->enum('status', ['pending', 'paid', 'failed', 'reversed'])->default('pending');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('payment_method')->nullable(); // 'wire_transfer', 'check', 'ach', etc.
            $table->string('reference_number')->nullable(); // Bank reference or check number
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
        Schema::dropIfExists('payment_transactions');
    }
};

