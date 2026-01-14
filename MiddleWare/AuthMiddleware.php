<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    //Genere le token

    public static function generateToken(array $user): string
    {
        return self::generateAccessToken($user);
    }

    public static function generateAccessToken(array $user, ?int $ttlSeconds = null): string
    {
        $secret = $_ENV['JWT_SECRET'] ?? '';
        if (!$secret) return '';

        $now = time();
        $payload = [
            'sub'   => $user['id'],
            'email' => $user['email'],
            'role'  => $user['roleId'],
            'iat'   => $now,
        ];

        $ttl = $ttlSeconds;
        if ($ttl === null) {
            $envTtl = (int)($_ENV['JWT_TTL'] ?? 0); // seconds; 0 => no exp
            $ttl = $envTtl > 0 ? $envTtl : 0;
        }

        if ($ttl > 0) {
            $payload['exp'] = $now + $ttl;
        }

        return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
    }

    public static function generateRefreshToken(): string
    {
        return bin2hex(random_bytes(64));
    }

    public static function hashToken(string $token): string
    {
        $secret = $_ENV['JWT_SECRET'] ?? '';
        return hash_hmac('sha256', $token, $secret);
    }

    public static function verifyToken(): array
    {
        header('Content-Type: application/json; charset=utf-8');

        // Récupère le token
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $token = str_starts_with($token, 'Bearer ') ? substr($token, 7) : null;
        if (!$token) {
            http_response_code(403);
            echo json_encode(['message' => 'Token manquant']);
            exit;
        }

        // Vérifie le token
        $secret = $_ENV['JWT_SECRET'] ?? '';
        if (!$secret) {
            http_response_code(500);
            echo json_encode(['message' => 'Configuration JWT manquante']);
            exit;
        }

        // Décode le token
        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $_REQUEST['user'] = json_decode(json_encode($decoded), true);
            return $_REQUEST['user'];
        } catch (\Throwable $e) {
            http_response_code(401);
            echo json_encode(['message' => 'Token invalide']);
            exit;
        }
    }
}
