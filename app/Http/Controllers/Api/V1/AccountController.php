<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    // Fetch all accounts for the authenticated user
    public function index()
    {
        // $accounts = Auth::guard('sanctum')->user()->accounts()->with(['outgoingTransactions', 'incomingTransactions'])->get();
        
        try{
            
            $accounts = Auth::guard('sanctum')->user()->accounts()->get(); // Assuming a user has many accounts
            // $accounts = Account::where('user_id', Auth::id())
            //         ->with(['outgoingTransactions', 'incomingTransactions'])
            //         ->get();
            return response()->json($accounts);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        
    }

    // Show a specific account's details
    public function show(string $accountNumber)
    {
        $account = Account::where('account_number', $accountNumber)->first();

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        return response()->json($account);
    }

    // Create a new account for the authenticated user
    public function store(StoreAccountRequest $request)
    {
        $account = Account::create([
            'account_number' => $this->generateAccountNumber(),
            'balance' => 0.00,
            'account_type' => $request->account_type,
            'user_id' => Auth::guard('sanctum')->id(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Account Created Successfully', 'data' => $account], 201);
    }

    /**
     * Generates dummy account number.
     * @return string
     */
    private function generateAccountNumber()
    {
        return (string) rand(1000000000, 9999999999);
    }
}

