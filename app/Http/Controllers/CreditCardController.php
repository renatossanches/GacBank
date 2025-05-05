<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditCardController extends Controller
{
    public function showCreditCard()
    {
        $user = Auth::user();
    
        $creditCard = $user->bankAccounts->creditCard;
    
        $hasCreditCard = $creditCard !== null;
        $limit = $hasCreditCard ? $creditCard->available_credit : null;
            
        return view('creditCard', [
            'hasCreditCard' => $hasCreditCard,
            'limit' => $limit,
        ]);
    }
    
    
    
}