<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JwtService;
use App\Services\JsonService;



class AuthController extends Controller
{
    public function login(Request $request, JsonService $json, JwtService $jwt)
    {
        $users = $json->read('users');

        $user = collect($users)->firstWhere('email', $request->email);

        if (!$user || !password_verify($request->password, $user['password'])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $jwt->generate($user);

        return response()->json([
            'token' => $token
        ]);
    }
}
