<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tag_id')->nullable()->constrained()->onDelete('set null');

            $table->enum('type', ['income', 'expense']);
            $table->decimal('value', 10, 2);
            $table->boolean('is_paid')->default(true);
            $table->date('date');
            $table->string('description');
            $table->text('notes')->nullable();

            // Novos campos para transações fixas e recorrentes
            $table->boolean('is_fixed')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->integer('installments')->nullable();
            $table->string('installment_period')->nullable(); // Ex: 'days', 'weeks', 'months'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
