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
        Schema::create('objectives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestampTz('date', 3);
            $table->decimal('initial_amount', 15, 2)->default(0);
            $table->decimal('target_amount', 15, 2);
            $table->string('icon');
            $table->string('color');
            $table->enum('status', ['actived', 'paused', 'finished'])->default('actived');
            $table->timestampsTz(3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectives');
    }
};
