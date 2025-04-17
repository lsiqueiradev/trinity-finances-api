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
        Schema::create('recurrences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->foreignUuid('account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->string('observation')->nullable();
            $table->enum('type', ['income', 'expense']);
            $table->string('frequency');
            $table->boolean('is_active')->default(true);
            $table->timestampsTz(3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurrences');
    }
};
