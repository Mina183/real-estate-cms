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
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->string('nationality')->nullable();
        $table->string('language')->nullable();
        $table->string('base_location')->nullable();
        $table->string('best_contact_method')->nullable();

        // Lead source
        $table->unsignedBigInteger('lead_source_id')->nullable();
        $table->foreign('lead_source_id')
            ->references('id')
            ->on('lead_sources')
            ->onDelete('set null');
        $table->boolean('is_investor')->nullable();
        $table->string('investor_type')->nullable(); // long-term / short-term
        $table->string('preferred_property_type')->nullable();
        $table->string('preferred_location')->nullable();
        $table->boolean('uae_visa_required')->nullable();

        // Financial
        $table->string('property_detail_type')->nullable(); // 1BR/2BR/villa/etc.
        $table->string('investment_type')->nullable(); // off-plan/secondary
        $table->decimal('investment_budget', 15, 2)->nullable();
        $table->string('employment_source')->nullable();
        $table->string('funds_location')->nullable();

        // CP remarks and funnel
        $table->text('cp_remarks')->nullable();
        $table->string('funnel_stage')->nullable();

        $table->foreignId('channel_partner_id')->constrained('users')->onDelete('cascade');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
