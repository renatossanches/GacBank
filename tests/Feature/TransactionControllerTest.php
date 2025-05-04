<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->sender = User::factory()->create(['cpf' => '11111111111']);
        $this->receiver = User::factory()->create(['cpf' => '22222222222']);
        
        BankAccount::factory()->create(['user_id' => $this->sender->id]);
        BankAccount::factory()->create(['user_id' => $this->receiver->id]);
        
        Wallet::factory()->create(['bank_accounts_id' => $this->sender->bankAccount->id, 'balance' => 200]);
        Wallet::factory()->create(['bank_accounts_id' => $this->receiver->bankAccount->id, 'balance' => 0]);
        
        Sanctum::actingAs($this->sender);
    }

    public function test_reverse_transaction()
    {
        // Primeiro faz uma transferência
        $transaction = Transaction::create([
            'bank_accounts_id' => $this->sender->bankAccount->id,
            'sender_cpf' => '11111111111',
            'receiver_cpf' => '22222222222',
            'amount' => 100,
            'type' => 'transfer',
            'status' => 'completed'
        ]);
        
        $response = $this->postJson("/api/transactions/{$transaction->id}/reverse");
        
        $response->assertStatus(200)
            ->assertJson(['message' => 'Transação revertida com sucesso']);
        
        $this->assertEquals('reversed', $transaction->fresh()->status);
        $this->assertEquals(200, $this->sender->bankAccount->wallet->fresh()->balance);
        $this->assertEquals(0, $this->receiver->bankAccount->wallet->fresh()->balance);
    }
}