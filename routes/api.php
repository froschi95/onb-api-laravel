<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function() {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
    });
    
    Route::middleware(['auth:sanctum'])->group(function () {
        
        Route::get('/', function () {
            return 'API';
        });

        // Account routes
        Route::get('/accounts', [AccountController::class, 'index']); // List all accounts
        Route::get('/account/{account}', [AccountController::class, 'show']); // Show a specific account
        Route::post('/accounts', [AccountController::class, 'store']); // Create an account
        
        // Transaction routes
        Route::post('/transfer', [TransactionController::class, 'transfer']); // Transfer money between accounts
        Route::post('/deposit', [TransactionController::class, 'deposit']);   // Deposit money
        Route::post('/withdraw', [TransactionController::class, 'withdraw']); // Withdraw money
        
        
        // Get authenticated user's information
        Route::get('/user', function (Request $request) {
            return $request->user() == Auth::guard('sanctum')->user();
        });
    
        // Logout (invalidate token)
        Route::post('/logout', [AuthController::class, 'logout']);
    });

});

