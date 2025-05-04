<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $primaryKey = 'bank_accounts_user_id';
    public $incrementing = false;
    

    protected $fillable = [
        'bank_accounts_user_id',
        'balance'
    ];

    public function bankAccounts()
    {
        return $this->belongsTo(BankAccount::class, 'user_id');
    }
    
    
}    
