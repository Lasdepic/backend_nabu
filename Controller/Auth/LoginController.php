<?php

require_once __DIR__ . '/../../DAO/UsersDAO.php';
require_once __DIR__ . '/../../MiddleWare/AuthMiddleware.php';
use Firebase\JWT\JWT;

class LoginController
{
    private UsersDAO $userDao;

    public function __construct(UsersDAO $userDao)
    {
        $this->userDao = $userDao;
    }
 public function login(): void
{
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['message' => 'Méthode non autorisée']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$email || !$password || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['message' => 'Email ou mot de passe invalide']);
        return;
    }

    $user = $this->userDao->findByEmail($email);

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Email ou mot de passe incorrect']);
        return;
    };

    // information du user avec son role
    $payload = [
        'id' => $user['id'],
        'email' => $user['email'],
        'roleId' => $user['roleId']
    ];

    // Génére le token a partir du middleWare
    $token = AuthMiddleware::generateToken($payload);

    // Place le token dans un cookie HttpOnly
    setcookie(
        'token',
        $token,
        [
            'expires' => time() + (12 * 60 * 60),
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]
    );
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie'
    ]);
}

    public function logout(): void
{
    session_start();

    $_SESSION = [];

    setcookie('token', '', [
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    session_destroy();

    echo json_encode([
        'success' => true,
        'message' => 'Déconnexion réussie'
    ]);
}
}