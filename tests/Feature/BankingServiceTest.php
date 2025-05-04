<?php
namespace Tests\Unit;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\Wallet;
use App\Http\Service\BankingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $bankingService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->bankingService = new BankingService();
        
        $this->user = User::factory()->create(['cpf' => '12345678901']);
        BankAccount::factory()->create(['user_id' => $this->user->id]);
        Wallet::factory()->create(['bank_accounts_id' => $this->user->bankAccount->id]);
    }

    public function test_deposit_increases_balance()
    {
        $this->bankingService->deposit('12345678901', 100);
        
        $this->assertEquals(100, $this->user->bankAccount->wallet->fresh()->balance);
    }

    public function test_transfer_updates_both_accounts()
    {
        $receiver = User::factory()->create(['cpf' => '98765432109']);
        BankAccount::factory()->create(['user_id' => $receiver->id]);
        Wallet::factory()->create(['bank_accounts_id' => $receiver->bankAccount->id]);
        
        // Primeiro deposita dinheiro
        $this->user->bankAccount->wallet->update(['balance' => 200]);
        
        $this->bankingService->transfer('12345678901', '98765432109', 100);
        
        $this->assertEquals(100, $this->user->bankAccount->wallet->fresh()->balance);
        $this->assertEquals(100, $receiver->bankAccount->wallet->fresh()->balance);
    }
}