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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained()->onDelete('cascade');
            $table->json('name');
            $table->string('slug');
            $table->json('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            // Unique constraint for taxonomy_id and slug
            $table->unique(['taxonomy_id', 'slug']);

            // Self-referencing foreign key for parent-child relationships
            $table->foreign('parent_id')
                ->references('id')
                ->on('terms')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
