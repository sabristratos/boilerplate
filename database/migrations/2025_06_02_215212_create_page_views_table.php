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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->index();
            $table->string('path', 255)->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser_name')->nullable();
            $table->string('platform_name')->nullable();
            $table->timestamp('visited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
