<?php
require_once __DIR__ . '/../../MiddleWare/AuthMiddleware.php';

class AuthController
{
    public function checkAuth(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $token = $_COOKIE['token'] ?? '';
        if (!$token) {
            echo json_encode(['authenticated' => false]);
            return;
        }
        try {
            $user = AuthMiddleware::verifyTokenFromCookie($token);
            echo json_encode([
                'authenticated' => true,
                'user' => [
                    'id' => $user['id'] ?? null,
                    'email' => $user['email'] ?? null,
                    'roleId' => $user['role'] ?? null
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['authenticated' => false]);
        }
    }
}
