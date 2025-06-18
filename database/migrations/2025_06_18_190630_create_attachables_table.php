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
        Schema::create('attachables', function (Blueprint $table) {
            $table->foreignId('attachment_id')->constrained()->onDelete('cascade');
            $table->morphs('attachable');
            $table->primary(['attachment_id', 'attachable_id', 'attachable_type'], 'attachables_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachables');
    }
};
