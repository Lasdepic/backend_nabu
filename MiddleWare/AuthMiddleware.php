<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    //Genere le token

    public static function generateToken(array $user): string
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

    return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
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
