<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (! $request->email || ! $request->password) {
            return response()->json([
                'error' => 'Tidak boleh kosong',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Email atau password salah',
            ], 401);
        }

        // Optional: delete old tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Ambil role dari Spatie
        $role = $user->getRoleNames()->first();

        return response()->json([
            'data' => 'OK',
            'token' => $token,
            'name' => $user->name,
            'role' => $role,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'data' => 'OK',
        ], 200);
    }
}
