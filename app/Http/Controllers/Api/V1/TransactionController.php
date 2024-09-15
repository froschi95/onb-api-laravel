<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use App\Http\Requests\TransferRequest;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Handle transfers between accounts
     * @param \App\Http\Requests\TransferRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function transfer(TransferRequest $request)
    {
        // Find accounts based on the account numbers provided in the request
        $fromAccount = Account::where('account_number', $request->from_account_number)->first();
        $toAccount = Account::where('account_number', $request->to_account_number)->first();

        // Ensure both accounts exist
        if (!$fromAccount || !$toAccount) {
            return response()->json(['message' => 'One or both accounts not found'], 404);
        }

        // Ensure the authenticated user owns the `fromAccount`
        if ($fromAccount->user_id !== Auth::guard('sanctum')->id()) {
            return response()->json(['message' => 'Unauthorized to transfer from this account'], 403);
        }

        // Ensure sufficient balance in the sender's account
        if ($fromAccount->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }

        // Perform the transfer
        $fromAccount->decrement('balance', $request->amount);
        $toAccount->increment('balance', $request->amount);

        // Log the transaction using the outgoingTransactions relationship
        $fromAccount->outgoingTransactions()->create([
            'receiver_account_id' => $toAccount->id,  // Correct field name
            'amount' => $request->amount,
            'type' => 'transfer',
        ]);

        return response()->json(['message' => 'Transfer Successful']);
    }

    /**
     * Handle Deposit transactions
     * @param DepositRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function deposit(DepositRequest $request)
    {
        // Find the recipient account
        $account = Account::where('account_number', $request->to_account_number)->first();

        // Increment balance
        $account->increment('balance', $request->amount);

        // Log the deposit transaction
        $account->incomingTransactions()->create([
            'sender_account_id' => null,  // No sender for deposits
            'amount' => $request->amount,
            'type' => 'deposit',
        ]);

        $newBalance = $account->balance;

        return response()->json(['message' => 'Deposit successful. Your Account balance is ' . (string) $newBalance]);
    }

    /**
     * Handle a withdrawal
     * @param WithdrawRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function withdraw(WithdrawRequest $request)
    {
        // Find the withdrawer's account
        $account = Account::where('account_number', $request->from_account_number)->first();

        // Ensure the authenticated user owns this account
        if ($account->user_id !== Auth::guard('sanctum')->id()) {
            return response()->json(['message' => 'Unauthorized to withdraw from this account'], 403);
        }

        // Ensure sufficient funds
        if ($account->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }

        // Decrement balance
        $account->decrement('balance', $request->amount);

        // Log the withdrawal transaction
        $account->outgoingTransactions()->create([
            'receiver_account_id' => null,  // No receiver for withdrawals
            'amount' => $request->amount,
            'type' => 'withdrawal',
        ]);

        return response()->json(['message' => 'Withdrawal successful']);
    }
}

