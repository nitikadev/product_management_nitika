<?php

namespace App\Http\Controllers\API;

use App\Http\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->validation_error_response($validator);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return ApiResponse::forbidden(__('Invalid credentials'));
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            return ApiResponse::ok(
                __('Logged in'),
                [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'user_details' => $user
                ]
            );
        } catch (\Exception $e) {
            Log::error("Login failed: " . $e->getMessage());
            return ApiResponse::error('Something went wrong! Please try again.');
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
            ]);

            if ($validator->fails()) {
                return $this->validation_error_response($validator);
            }

            // Generate temporary password
            $temporaryPassword = Str::random(10);

            // Create new user with temporary password
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($temporaryPassword),
            ]);
            return ApiResponse::ok(
                __('User registered successfully.'),
                [
                    'temporary_password' => $temporaryPassword
                ]
            );
        } catch (\Exception $e) {
            Log::error("Login failed: " . $e->getMessage());
            return ApiResponse::error('Something went wrong! Please try again.');
        }
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $token);
        JWTAuth::setToken($token)->invalidate();
        Auth::logout();
        return ApiResponse::ok(
            __('Logout Successfully')
        );
    }
}
