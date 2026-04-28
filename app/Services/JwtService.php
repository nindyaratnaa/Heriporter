<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private $secret = "SECRET_KEY_KAMU";

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