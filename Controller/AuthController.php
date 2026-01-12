<?php

require_once __DIR__ . '/../DAO/UsersDAO.php';

class AuthController
{
    private UsersDAO $userDao;

    public function __construct(UsersDAO $userDao)
    {
        $this->userDao = $userDao;
    }

    public function register(): void
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

        $data = stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false
            ? json_decode(file_get_contents('php://input'), true)
            : $_POST;

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Données invalides'
            ]);
            return;
        }

        $lastName  = trim($data['nom'] ?? '');
        $firstName = trim($data['prenom'] ?? '');
        $email     = trim($data['email'] ?? '');
        $password  = $data['password'] ?? '';
        $roleId    = filter_var($data['roleId'] ?? null, FILTER_VALIDATE_INT);

        if (
            $lastName === '' ||
            $firstName === '' ||
            $email === '' ||
            $password === '' ||
            $roleId === false
        ) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Champs manquants ou invalides'
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

        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Mot de passe trop faible'
            ]);
            return;
        }

        if ($this->userDao->emailExists($email)) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'Cet email est déjà utilisé'
            ]);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if (!$this->userDao->createUsers(
            $lastName,
            $firstName,
            $email,
            $passwordHash,
            $roleId
        )) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => "Erreur lors de l'inscription"
            ]);
            return;
        }

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Inscription réussie'
        ]);
    }
}