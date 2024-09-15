<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['sender_account_id', 'receiver_account_id', 'amount', 'type'];

    // Define the sender account relationship
    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'sender_account_id');
    }

    // Define the recipient account relationship
    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'receiver_account_id');
    }
}
