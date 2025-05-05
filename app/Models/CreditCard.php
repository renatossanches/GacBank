<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    protected $primaryKey = 'bank_accounts_user_id';
    public $incrementing = false;

    protected $fillable = [
        'bank_accounts_user_id',
        'card_number',
        'card_holder',
        'cvv',
        'expiration_date',
        'limit',
        'balance',
        'available_credit',
    ];
}
