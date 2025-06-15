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
        Schema::create('notification_digests', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable');
            $table->string('notification_type');
            $table->json('data');
            $table->string('frequency'); // daily, weekly
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_digests');
    }
};
