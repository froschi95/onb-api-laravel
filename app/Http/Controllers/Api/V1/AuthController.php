<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Http\Helper\ResponseHelper;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Register New User and store in the db
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
            ]);

            if ($user) {
                return ResponseHelper::success(message: 'User has been registered successfully', data: $user, statusCode: 201);
            }
            return ResponseHelper::error(message: 'Unable to register user, please try again', statusCode: 400);

        } catch (Exception $e) {
            Log::error('Unable to register user : ' . $e->getMessage() . ' - Line No ' . $e->getLine());
            return ResponseHelper::error(message: 'Unable to register user, please try again' . $e->getMessage(), statusCode: 500);
           
        }
    }

    /**
     * Login a user
     * @param LoginRequest $request
     * @return JSONResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return ResponseHelper::error(message: 'Invalid Credentials', statusCode: 400);
            }

            $user = Auth::user();

            // Create API token
            $token = $user->createToken('My API Token')->plainTextToken;

            $authUser = [
                'user' => $user,
                'token' => $token,
            ];

            return ResponseHelper::success(message: 'You are logged in successfully', data: $authUser, statusCode: 200);

        } catch (Exception $e) {
            Log::error('Unable to login user : ' . $e->getMessage() . ' - Line No ' . $e->getLine());
            return ResponseHelper::error(message: 'Unable to login user, please try again' . $e->getMessage(), statusCode: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ResponseHelper::success(message: 'Logged out successfully', data: [], statusCode: 204);
    }
}
