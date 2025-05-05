<?php

namespace App\Http\Service;
use App\Models\CreditCard;
use Illuminate\Support\Facades\Auth;
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

    public function transfer($senderCpf, $receiverCpf, $amount, $description = '', $forceCredit = false)
    {
        DB::transaction(function () use ($senderCpf, $receiverCpf, $amount, $description, $forceCredit) {
            $sender = User::where('cpf', $senderCpf)->firstOrFail();
            $receiver = User::where('cpf', $receiverCpf)->firstOrFail();
    
            $senderAccounts = $sender->bankAccounts;
            $receiverAccounts = $receiver->bankAccounts;
    
            $senderWallet = $senderAccounts->wallet;
            $receiverWallet = $receiverAccounts->wallet;
            $senderCreditCard = $senderAccounts->creditCard;
    
            // Se for para usar o cartão de crédito, adiciona 10% ao valor apenas para o remetente
            if ($forceCredit) {
                $amountToDeduct = $amount * 1.10;  // O remetente perde 10% a mais
            } else {
                $amountToDeduct = $amount;  // Caso contrário, o valor permanece o mesmo
            }
    
            // Verificação de saldo ou crédito disponível
            if ($senderWallet->balance >= $amountToDeduct) {
                $senderWallet->balance -= $amountToDeduct;
                $senderWallet->save();
                $transactionType = 'transfer';
            } elseif ($forceCredit && $senderCreditCard->available_credit >= $amountToDeduct) {
                $senderCreditCard->available_credit -= $amountToDeduct;
                $senderCreditCard->save();
                $transactionType = 'transfer_credit';
            } else {
                throw new \Exception('Saldo insuficiente e crédito não autorizado.');
            }
    
            // O destinatário recebe o valor original
            $receiverWallet->balance += $amount;
            $receiverWallet->save();
    
            // Criar a transação no banco de dados
            $this->createTransaction2(
                $senderAccounts->user_id,
                $senderCpf,
                $receiverCpf,
                $amount,
                $transactionType,
                $description
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
    
            // Lógica para transações do tipo 'transfer_credit' (transação usando crédito)
            if ($transaction->type === 'transfer_credit') {
                $senderCreditCard = $sender->bankAccounts->creditCard;
                $receiverWallet = $receiver->bankAccounts->wallet;
    
                if ($receiverWallet->balance < $transaction->amount) {
                    throw new \Exception('O destinatário não tem saldo suficiente para a reversão.');
                }
    
                // Reverte o valor para o crédito do remetente
                $amountToRevert = $transaction->amount * 1.10; 
                $senderCreditCard->available_credit += $amountToRevert;
                $senderCreditCard->save();
    
                // Atualiza o saldo da carteira do destinatário
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

    public function createCreditCard()
    {
        $user = Auth::user();
        $bankAccount = $user->bankAccounts;

        $existingCard = CreditCard::where('bank_accounts_user_id', $bankAccount->user_id)->first();
        if ($existingCard) {
            return ['error' => 'Você já possui um cartão de crédito.'];
        }
        $creditCard = new CreditCard();
        $creditCard->bank_accounts_user_id = $user->id;
        $creditCard->card_number = $this->generateCardNumber();
        $creditCard->card_holder = $user->name; 
        $creditCard->cvv = rand(100, 999);
        $creditCard->expiration_date = now()->addYears(5);
        $creditCard->limit = 5000;
        $creditCard->balance = 5000;
        $creditCard->available_credit = 5000;
        $creditCard->save();
    }

    private function generateCardNumber()
    {
        $bin = '400000';
        $number = $bin;

        for ($i = 0; $i < 9; $i++) {
            $number .= rand(0, 9);
        }

        $number .= $this->calculateLuhn($number);
        return $number;
    }

    private function calculateLuhn($number)
    {
        $sum = 0;
        $alt = true;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $n = intval($number[$i]);
            if ($alt) {
                $n *= 2;
                if ($n > 9) $n -= 9;
            }
            $sum += $n;
            $alt = !$alt;
        }
        return (10 - ($sum % 10)) % 10;
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