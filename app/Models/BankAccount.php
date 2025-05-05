<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'number_account'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'bank_accounts_user_id');
    }
    
    public function creditCard()
    {
        return $this->hasOne(CreditCard::class, 'bank_accounts_user_id', 'user_id');
    }
    
    
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'bank_accounts_user_id');
    }

}
