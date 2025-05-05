<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_accounts_user_id');
            $table->string('sender_cpf', 14);
            $table->string('receiver_cpf', 14)->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['deposit', 'transfer', 'transfer_credit']);
            $table->enum('status', ['pending', 'completed', 'reversed', 'failed'])->default('completed');
            $table->string('description')->nullable();
            $table->string('reversal_reason')->nullable();

        
            $table->foreign('bank_accounts_user_id')
                ->references('user_id')
                ->on('bank_accounts')
                ->onDelete('cascade');
        
            $table->foreign('sender_cpf')
                ->references('cpf')
                ->on('users')
                ->onDelete('restrict');
        
            $table->foreign('receiver_cpf')
                ->references('cpf')
                ->on('users')
                ->onDelete('restrict');
                $table->timestamps();
        });
        
    }
    
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
