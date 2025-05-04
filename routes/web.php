<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BankingController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

route::get('/users', function () {
    return "random";
});

Route::get('/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
});
Route::middleware('auth')->group(function () {
Route::get('/deposit', [TransactionController::class, 'showDepositForm'])->name('deposit.form');
Route::post('/deposit', action: [BankingController::class, 'deposit'])->name('deposit');
Route::get('/transfer', [TransactionController::class, 'showTransferForm'])->name('transfer.form');
Route::post('/transfer', [BankingController::class, 'transfer'])->name('transfer');
Route::get('/transactions', [BankingController::class, 'getUserTransactions'])->name('transactions.list');
Route::post('/banking/reverse/{id}', [BankingController::class, 'reverse'])->name('banking.reverse');
});




Route::get('/dashboard', [DashboardController::class, 'getBalance'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
