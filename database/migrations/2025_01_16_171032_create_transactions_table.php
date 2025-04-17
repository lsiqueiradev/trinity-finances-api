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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            // $table->foreignUuid('card_id')->nullable()->constrained('cards')->nullOnDelete();
            // $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('description');
            $table->string('observation')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['expense', 'income']);
            $table->timestampTz('date');
            $table->string('frequency')->nullable();
            $table->uuid('recurrence_id')->nullable()->index();
            $table->integer('total_installments')->nullable();
            $table->integer('current_installments')->nullable();
            $table->uuid('installments_id')->nullable()->index();
            $table->boolean('is_installments')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->boolean('is_confirmed')->default(false);
            $table->timestampsTz(3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
