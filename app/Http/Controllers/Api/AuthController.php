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
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        $token = method_exists($user, 'createToken')
            ? $user->createToken('web')->plainTextToken
            : null;

        return response()->json([
            'data' => $user,
            'token' => $token,
        ]);
    }
}
