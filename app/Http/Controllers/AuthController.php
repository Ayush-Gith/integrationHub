<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            // Generate token with custom claims
            $claims = ['name' => $user->name, 'email' => $user->email, "role" => $user->role, "is_admin" => $user->is_admin];
            if (!$token = JWTAuth::claims($claims)->fromUser($user)) {
                return response()->json(['error' => 'Unable to generate token'], 500);
            }

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);

        } catch (\Throwable $e) {
            Log::error('AuthController.register failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Registration failed'], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            $claims = [];
            if ($request->filled('email')) {
                $user = \App\Models\User::where('email', $request->input('email'))->first();
                if ($user) {
                    $claims = ['name' => $user->name, 'email' => $user->email, "role" => $user->role, "is_admin" => $user->is_admin];
                }
            }

            if (!$token = JWTAuth::claims($claims)->attempt($credentials)) { // ✅ force api guard
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            return response()->json(['token' => $token]);

        } catch (\Throwable $e) {
            Log::error('AuthController.login failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Login failed'], 500);
        }
    }

    public function me()
    {
        try {
            return response()->json(auth()->user());
        } catch (\Throwable $e) {
            Log::error('AuthController.me failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to retrieve user'], 500);
        }
    }
}
