<?php

namespace App\Http\Controllers;

use App\Http\Service\BankingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function showDepositForm()
{
    return view('deposit'); 
}

public function showTransferForm()
{
    return view('transfer'); 
}


public function dashboard()
{
    return view('dashboard');
}



}
