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
        Schema::create('investor_stage_transitions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('investor_id')->constrained('investors')->cascadeOnDelete();
            
            // Stage transition
            $table->string('from_stage')->nullable();
            $table->string('to_stage');
            
            // Status transition
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            
            // Who made the change
            $table->foreignId('changed_by_user_id')->constrained('users');
            
            // Why
            $table->text('reason')->nullable();
            
            // Additional context
            $table->json('metadata')->nullable();
            
            $table->timestamp('transitioned_at');
            
            $table->index(['investor_id', 'transitioned_at']);
            $table->index('to_stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_stage_transitions');
    }
};
