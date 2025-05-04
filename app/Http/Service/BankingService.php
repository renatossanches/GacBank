<?php

namespace App\Http\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Transaction;

class BankingService
{
    public function deposit($cpf, $amount)
    {
        DB::transaction(function () use ($cpf, $amount) {
            $user = User::where('cpf', $cpf)->firstOrFail();
            $wallet = $user->bankAccounts->wallet;

            $wallet->balance += $amount;
            $wallet->save();

            $this->createTransaction(
                $user->bankAccounts->user_id,
                $cpf,
                null,
                $amount,
                'deposit'
            );
        });
    }

    public function transfer($senderCpf, $receiverCpf, $amount, $description = '')
    {
        DB::transaction(function () use ($senderCpf, $receiverCpf, $amount, $description) {
            $sender = User::where('cpf', $senderCpf)->firstOrFail();
            $receiver = User::where('cpf', $receiverCpf)->firstOrFail();
    
            $senderAccounts = $sender->bankAccounts;
            $receiverAccounts = $receiver->bankAccounts;
    
            if (!$senderAccounts || !$receiverAccounts) {
                throw new \Exception('Conta bancária não encontrada para remetente ou destinatário');
            }
    
            $senderWallet = $senderAccounts->wallet;
            $receiverWallet = $receiverAccounts->wallet;
    
            if (!$senderWallet || !$receiverWallet) {
                throw new \Exception('Carteira não encontrada para remetente ou destinatário');
            }
    
            if ($senderWallet->balance < $amount) {
                throw new \Exception('Saldo insuficiente');
            }
    
            $senderWallet->balance -= $amount;
            $senderWallet->save();
    
            $receiverWallet->balance += $amount;
            $receiverWallet->save();
    
            // Cria a transação com a descrição
            $this->createTransaction2(
                $senderAccounts->user_id,
                $senderCpf,
                $receiverCpf,
                $amount,
                'transfer',
                $description // Aqui é onde a descrição será salva
            );
        });
    }
    
    
    

    public function reverseTransaction($transactionId)
    {
        // Inicia a transação DB
        DB::transaction(function () use ($transactionId) {
            $transaction = Transaction::findOrFail($transactionId);
    
            // Verifica se a transação já foi revertida
            if ($transaction->status === 'reversed') {
                throw new \Exception('Transação já foi revertida.');
            }
    
            // Verifica se a transação é um depósito
            if ($transaction->type == 'deposit') {
                throw new \Exception('Depósitos não podem ser revertidos.');
            }
    
            // Recupera os dados do remetente e destinatário
            $sender = $transaction->sender_cpf ? User::where('cpf', $transaction->sender_cpf)->firstOrFail() : null;
            $receiver = $transaction->receiver_cpf ? User::where('cpf', $transaction->receiver_cpf)->firstOrFail() : null;
    
            // Lógica para transações do tipo 'transfer'
            if ($transaction->type === 'transfer') {
                $senderWallet = $sender->bankAccounts->wallet;
                $receiverWallet = $receiver->bankAccounts->wallet;
    
                if ($receiverWallet->balance < $transaction->amount) {
                    throw new \Exception('O destinatário não tem saldo suficiente para a reversão.');
                }
    
                // Atualiza os saldos das carteiras
                $receiverWallet->balance -= $transaction->amount;
                $receiverWallet->save();
    
                $senderWallet->balance += $transaction->amount;
                $senderWallet->save();
            }
    
            // Lógica para transações do tipo 'deposit'
            if ($transaction->type === 'deposit') {
                $receiverWallet = $receiver->bankAccounts->wallet;
    
                if ($receiverWallet->balance < $transaction->amount) {
                    throw new \Exception('O usuário não tem saldo suficiente para reverter o depósito.');
                }
    
                // Atualiza o saldo do destinatário
                $receiverWallet->balance -= $transaction->amount;
                $receiverWallet->save();
            }
    
            // Marca a transação como revertida
            $transaction->status = 'reversed';
            $transaction->save();
        });
    
        // Se a transação foi revertida com sucesso
        return ['success' => true, 'message' => 'Transação revertida com sucesso.'];
    }
    
    


    public function getBalance($user)
    {
        // Obtenha o BankAccount do usuário
        $bankAccount = $user->bankAccount;

        // Se o BankAccount existir, obtenha o Wallet e o balance
        if ($bankAccount && $bankAccount->wallet) {
            return $bankAccount->wallet->balance;
        }

        // Se não houver BankAccount ou Wallet, retorne 0
        return 0;
    }

    public function getTransactionsByCpf($cpf)
    {
        $transactions = Transaction::where('sender_cpf', $cpf)
            ->orWhere('receiver_cpf', $cpf)
            ->orderBy('created_at', 'desc')
            ->get();

        return $transactions->map(function ($transaction) {
            $sender = User::where('cpf', $transaction->sender_cpf)->first();
            $receiver = User::where('cpf', $transaction->receiver_cpf)->first();

            return [
                'id' => $transaction->id,
                'sender_cpf' => $transaction->sender_cpf,
                'sender_name' => $sender ? $sender->name : 'Desconhecido',
                'receiver_cpf' => $transaction->receiver_cpf,
                'receiver_name' => $receiver ? $receiver->name : 'Desconhecido',
                'amount' => number_format($transaction->amount, 2, ',', '.'),
                'type' => $transaction->type,
                'status' => $transaction->status,
                'description' => $transaction->description,
                'created_at' => $transaction->created_at->format('d/m/Y H:i'),
            ];
        });
    }

    protected function createTransaction($bankAccountsUserId, $senderCpf, $receiverCpf, $amount, $type)
    {
        // Lógica para criar a transação
        Transaction::create([
            'bank_accounts_user_id' => $bankAccountsUserId,
            'sender_cpf' => $senderCpf,
            'receiver_cpf' => $receiverCpf,
            'amount' => $amount,
            'type' => $type,
        ]);
    }
    protected function createTransaction2($bankAccountUserId, $senderCpf, $receiverCpf, $amount, $type, $description = '')
    {
        // Lógica para criar a transação
        Transaction::create([
            'bank_accounts_user_id' => $bankAccountUserId,
            'sender_cpf' => $senderCpf,
            'receiver_cpf' => $receiverCpf,
            'amount' => $amount,
            'type' => $type,
            'description' => $description, 
            'status' => 'completed', 
        ]);
    }

}