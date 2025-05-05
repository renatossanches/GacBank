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
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_accounts_user_id')->primary();
            $table->string('card_number');
            $table->string('card_holder');
            $table->string('cvv', 3);
            $table->date('expiration_date');
            $table->decimal('limit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('available_credit', 15, 2)->default(0);
            $table->timestamps();
        
            $table->foreign('bank_accounts_user_id')
                ->references('user_id')
                ->on('bank_accounts')
                ->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
