 <?php

require_once __DIR__ . '/../../DAO/UsersDAO.php';

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
        echo json_encode([
            'success' => false,
            'message' => 'Méthode non autorisée'
        ]);
        return;
    }

    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_contains($contentType, 'application/json')) {
        $data = json_decode(file_get_contents('php://input'), true);
    } else {
        $data = $_POST;
    }

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Données invalides'
        ]);
        return;
    }

    $email    = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if ($email === '' || $password === '') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email et mot de passe requis'
        ]);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email invalide'
        ]);
        return;
    }

    $user = $this->userDao->findByEmail($email);

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Email ou mot de passe incorrect'
        ]);
        return;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'     => $user['id'],
        'email'  => $user['email'],
        'nom'    => $user['nom'],
        'prenom' => $user['prenom'],
        'role'   => $user['roleId']
    ];

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie'
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