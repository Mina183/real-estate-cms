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
    Schema::create('auth_logs', function (Blueprint $table) {
        $table->id();
        $table->string('guard'); // 'web' za staff, 'investor' za investitore
        $table->foreignId('user_id')->nullable(); // nullable jer failed login nema user_id
        $table->string('email'); // uvek logujemo email koji je pokušan
        $table->enum('event', ['login_success', 'login_failed', 'logout']);
        $table->string('ip_address', 45);
        $table->text('user_agent')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamp('created_at');
        
        $table->index(['guard', 'user_id']);
        $table->index(['email', 'event']);
        $table->index('created_at');
    });
}

public function down(): void
{
    Schema::dropIfExists('auth_logs');
}
};
