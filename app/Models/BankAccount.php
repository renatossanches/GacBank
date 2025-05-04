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
        // outros campos que podem ser preenchidos em massa
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'bank_accounts_user_id');
    }
    
    public function creditCards()
    {
        return $this->hasMany(CreditCard::class, 'bank_accounts_user_id');
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'bank_accounts_user_id');
    }

}
