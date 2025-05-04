<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /* CLASSE UTILIZADA APENAS PARA TESTES NO POSTMAN */

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'cpf' => 'required|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);
    
        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'cpf' => $validated['cpf'],
                'password' => Hash::make($validated['password']),
            ]);
    
            $bankAccounts = BankAccount::create([
                'user_id' => $user->id,
                'number_account' => rand(100000, 999999)
            ]);
    
            Wallet::create([
                'bank_accounts_user_id' => $bankAccounts->user_id,
                'balance' => 0
            ]);
        });
    
        return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
    }
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['As credenciais fornecidas estão incorretas.'],
        ]);
    }

    return response()->json([
        'token' => $user->createToken('API Token')->plainTextToken,
        'user' => $user
    ]);
}
}