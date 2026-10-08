<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi inputan form
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cek apakah email & password cocok di database
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // 3. Buat "kunci/token" khusus untuk admin ini
            $token = $user->createToken('admin-token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => $user
            ]);
        }

        // Jika salah password/email
        return response()->json([
            'status' => 'error',
            'message' => 'Email atau Password salah!'
        ], 401);
    }
}
