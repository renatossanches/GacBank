<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\Wallet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'cpf' => ['required', 'string', 'max:14', 'unique:users'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    try {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'password' => Hash::make($request->password),
            ]);

            $bankAccount = BankAccount::create([
                'user_id' => $user->id,
                'number_account' => rand(100000, 999999),
            ]);

            Wallet::create([
                'bank_accounts_user_id' => $bankAccount->user_id,
                'balance' => 0,
            ]);

            event(new Registered($user));
            Auth::login($user);
        });
    } catch (\Exception $e) {
        Log::error('Erro no registro: ' . $e->getMessage());
        return redirect()->back()->withErrors(['register' => 'Erro ao registrar usuário.']);
    }

    return redirect(route('dashboard', absolute: false));
}


}
