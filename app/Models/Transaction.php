<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = true; // Certifique-se de que está true (padrão)

    protected $dateFormat = 'Y-m-d H:i:s';
    
    protected $fillable = [
        'bank_accounts_user_id',
        'sender_cpf',
        'receiver_cpf',
        'amount',
        'type',
        'status',
        'description',
        'reversal_reason'
    ];
}