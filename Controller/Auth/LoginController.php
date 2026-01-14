 <?php

require_once __DIR__ . '/../../DAO/UsersDAO.php';
require_once __DIR__ . '/../../DAO/Auth/RefreshTokenDAO.php';
require_once __DIR__ . '/../../MiddleWare/AuthMiddleware.php';
use Firebase\JWT\JWT;

class LoginController
{
    private UsersDAO $userDao;
    private RefreshTokenDAO $refreshDao;

    public function __construct(UsersDAO $userDao, RefreshTokenDAO $refreshDao)
    {
        $this->userDao = $userDao;
        $this->refreshDao = $refreshDao;
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

    // Génére les tokens
    $accessTtl = (int)($_ENV['JWT_TTL'] ?? 900); // 15 min par défaut
    $token = AuthMiddleware::generateAccessToken($user, $accessTtl);
    $refreshToken = AuthMiddleware::generateRefreshToken();

    // Enregistre le refresh token
    $this->refreshDao->saveRefreshToken(
        (int)$user['id'],
        $refreshToken,
        (int)($_ENV['REFRESH_TTL_DAYS'] ?? 30),
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    );

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie',
        'token'   => $token,
        'accessToken' => $token,
        'refreshToken' => $refreshToken,
        'expiresIn' => $accessTtl > 0 ? $accessTtl : null
    ]);
}

    public function logout(): void
    {
        session_start();
        session_destroy();
        echo json_encode([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }
}