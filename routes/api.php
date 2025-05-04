<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BankingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/* !!! Classe usada apenas para fins de testes no Postman !!! */

/* Pode ser usado tanto RegisteredUserController como AuthController para o registro */

/*
Route::post('/store', [RegisteredUserController::class, 'store']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/deposit', [BankingController::class, 'deposit']);

Route::post('/reverse-transaction', [BankingController::class, 'reverseTransaction']);

Route::get('/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
});

*/