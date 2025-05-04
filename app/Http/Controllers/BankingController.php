<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Service\BankingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class BankingController extends Controller
{
    protected $bankingService;

    public function __construct(BankingService $bankingService)
    {
        $this->bankingService = $bankingService;
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);
    
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }
        $cpf = $user->cpf;
        $amount = $request->input('amount');
    
        $this->bankingService->deposit($user->cpf, $amount);
    
        return redirect()->route('deposit.form')->with('success', 'Depósito realizado com sucesso!');
    }
    
    
    public function transfer(Request $request)
    {
        $request->validate([
            'receiver_cpf' => 'required',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
        ]);
    
        $sender_cpf = auth()->user()->cpf;
        $receiver_cpf = $request->input('receiver_cpf');
        $description = $request->input('description', ''); 
        $amount = $request->input('amount');
    
        try {
            $this->bankingService->transfer($sender_cpf, $receiver_cpf, $amount, $description);
            return redirect()->route('transfer.form')->with('success', 'Transferência realizada com sucesso');
        } catch (\Exception $e) {
            return redirect()->route('transfer.form')->with('error', $e->getMessage());
        }
    }
    
    
    
    public function reverse($transactionId)
    {
        try {
            // Chama o serviço para reverter a transação
            $result = app(BankingService::class)->reverseTransaction($transactionId);
    
            // Se a reversão foi bem-sucedida
            return redirect()->route('transactions.list')->with('success', $result['message']);
        } catch (\Exception $e) {
            // Se houve algum erro (por exemplo, o erro de depósito não pode ser revertido)
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    

    public function getUserTransactions()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuário não autenticado');
        }
    
        $cpf = $user->cpf;
    
        $transactions = $this->bankingService->getTransactionsByCpf($cpf);
    
        return view('transactions', ['transactions' => $transactions]);
    }
    
    protected function createTransaction($bankAccountId, $senderCpf, $receiverCpf, $amount, $type)
    {
        return Transaction::create([
            'bank_accounts_id' => $bankAccountId,
            'sender_cpf' => $senderCpf,
            'receiver_cpf' => $receiverCpf,
            'amount' => $amount,
            'type' => $type,
            'status' => 'completed'
        ]);
    }

}