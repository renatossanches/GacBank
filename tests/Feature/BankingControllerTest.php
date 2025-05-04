<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class BankingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['cpf' => '12345678901']);
        BankAccount::factory()->create(['user_id' => $this->user->id]);
        Wallet::factory()->create(['bank_accounts_id' => $this->user->bankAccount->id]);
        
        Sanctum::actingAs($this->user);
    }

    public function test_deposit_money()
    {
        $response = $this->postJson('/api/deposit', [
            'cpf' => '12345678901',
            'amount' => 100.50
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Depósito realizado com sucesso']);
        
        $this->assertEquals(100.50, $this->user->bankAccount->wallet->fresh()->balance);
    }

    public function test_transfer_money()
    {
        $receiver = User::factory()->create(['cpf' => '98765432109']);
        BankAccount::factory()->create(['user_id' => $receiver->id]);
        Wallet::factory()->create(['bank_accounts_id' => $receiver->bankAccount->id, 'balance' => 0]);
        
        // Primeiro faz um depósito
        $this->user->bankAccount->wallet->update(['balance' => 200]);
        
        $response = $this->postJson('/api/transfer', [
            'receiver_cpf' => '98765432109',
            'amount' => 100
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transferência realizada com sucesso']);
        
        $this->assertEquals(100, $this->user->bankAccount->wallet->fresh()->balance);
        $this->assertEquals(100, $receiver->bankAccount->wallet->fresh()->balance);
    }

    public function test_transfer_with_insufficient_balance()
    {
        $receiver = User::factory()->create(['cpf' => '98765432109']);
        BankAccount::factory()->create(['user_id' => $receiver->id]);
        Wallet::factory()->create(['bank_accounts_id' => $receiver->bankAccount->id]);
        
        $response = $this->postJson('/api/transfer', [
            'receiver_cpf' => '98765432109',
            'amount' => 100
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Saldo insuficiente']);
    }
}