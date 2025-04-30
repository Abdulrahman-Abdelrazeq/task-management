<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Traits\Response;

class AuthController extends Controller
{
    use Response;

    // Register a new user.
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
        return $this->sendRes(true, 'User Created Successfully', [
            'user' => $user,
            'token' => $token,
        ], null, 201);
    }

    // Log in a user and issue a token.
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('api')->plainTextToken;

            return $this->sendRes(true, 'Login Successful', [
                'user' => $user,
                'token' => $token,
            ]);
        }

        return $this->sendRes(false, 'Unauthorized', null, null, 401);
    }

    // Log out a user and revoke the current token.
    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->sendRes(true, 'Logged out successfully');
    }
}