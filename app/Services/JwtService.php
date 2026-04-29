<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private $secret;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', 'hogwarts-secret-key-2024');
    }

    public function generate($user)
    {
        $payload = [
            'iss' => "hogwarts-api",
            'sub' => $user['id'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + 3600 // 1 jam
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function verify($token)
    {
        return JWT::decode($token, new Key($this->secret, 'HS256'));
    }
}