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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_group_id')->constrained()->onDelete('cascade');
            $table->string('key')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, select, checkbox, color, file, etc.
            $table->json('options')->nullable(); // For select, radio, etc.
            $table->boolean('is_public')->default(false); // Whether this setting is accessible via API
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
