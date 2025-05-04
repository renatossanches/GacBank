<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getBalance()
    {
        // Obter o usuário autenticado
        $user = Auth::user();
        
        // Obter o saldo da carteira do usuário
        $balance = $user->bankAccounts->wallet->balance;
        
        return view('dashboard', [
            'balance' => $balance
        ]);
    }
    
}
