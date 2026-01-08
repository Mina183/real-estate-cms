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
        Schema::create('document_approval_workflows', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('document_id')->constrained('data_room_documents')->cascadeOnDelete();
            
            $table->enum('workflow_status', [
                'pending',
                'under_review',
                'approved',
                'rejected',
                'revision_requested'
            ])->default('pending');
            
            $table->foreignId('submitted_by_user_id')->constrained('users');
            $table->timestamp('submitted_at');
            
            $table->foreignId('reviewer_user_id')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            
            $table->foreignId('approver_user_id')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->text('reviewer_comments')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->json('required_metadata_checklist')->nullable();
            $table->boolean('metadata_complete')->default(false);
            
            $table->timestamps();
            
            $table->index(['document_id', 'workflow_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_approval_workflows');
    }
};
