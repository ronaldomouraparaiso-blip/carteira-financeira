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
            $table->id();
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('type', ['deposit', 'transfer']); // tipo de transação
            $table->decimal('amount', 15, 2); // valor da transação
            $table->enum('status', ['completed', 'reversed'])->default('completed'); // status da transação
            $table->text('description')->nullable(); // descrição opcional
            $table->timestamp('reversed_at')->nullable(); // data de reversão
            $table->timestamps();
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
