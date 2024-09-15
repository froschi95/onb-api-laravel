<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_number',
        'balance',
        'account_type',
        'user_id'
    ];

    // Relation to User: One user can have multiple accounts
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outgoingTransactions()
    {
        return $this->hasMany(Transaction::class, 'sender_account_id');
    }

    public function incomingTransactions()
    {
        return $this->hasMany(Transaction::class, 'receiver_account_id');
    }

    // Get all transactions where the account is either sender or recipient
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'sender_account_id')
                ->orWhere('receiver_account_id', $this->id); // $this->outgoingTransactions->merge($this->incomingTransactions);
    }
}
