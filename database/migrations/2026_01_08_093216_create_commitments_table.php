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
        Schema::create('commitments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('investor_id')->constrained('investors')->cascadeOnDelete();
            $table->foreignId('fund_id')->constrained('funds')->cascadeOnDelete();
            
            $table->decimal('committed_amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('commitment_date');
            
            $table->enum('status', [
                'pending',
                'active',
                'fulfilled',
                'cancelled'
            ])->default('pending');
            
            $table->decimal('funded_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['investor_id', 'fund_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commitments');
    }
};
