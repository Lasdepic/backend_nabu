<?php

require_once __DIR__ . '/../DAO/UsersDAO.php';

class UsersController

{
    private UsersDAO $usersDAO;

    public function __construct(UsersDAO $usersDAO)
    {
        $this->usersDAO = $usersDAO;
    }

    // Afficher tous les users
    public function getAllUsers(): void
    {
        header('Content-Type: application/json');

        try {
            $users = $this->usersDAO->getAllUsers();

            if (empty($users)) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Aucun utilisateur trouvé'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs'
            ]);
        }
    }

    // Afficher un user par ID
    public function getUserById(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $user = $this->usersDAO->findById($id);

            if (!$user) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ]);
                return;
            }

            // ne retourne pas le mot de passe
            unset($user['password']);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'utilisateur'
            ]);
        }
    }

    // récupère un user
    public function handleGetUserRequest(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID manquant'
            ]);
            return;
        }

        $this->getUserById((int)$id);
    }

    // EDITION USER

    // Modifier un utilisateur (hors mot de passe)
    public function updateUser(): void
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        $nom = $input['nom'] ?? null;
        $prenom = $input['prenom'] ?? null;
        $email = $input['email'] ?? null;
        $roleId = $input['roleId'] ?? null;
        if (!$id || !$nom || !$prenom || !$email || !$roleId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            return;
        }
        $success = $this->usersDAO->updateUser((int)$id, $nom, $prenom, $email, null, (int)$roleId);
        if ($success) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Utilisateur modifié']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification']);
        }
    }

    // Modifier uniquement le mot de passe d'un utilisateur
    public function updateUserPassword(): void
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        $password = $input['password'] ?? null;
        if (!$id || !$password) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            return;
        }
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $success = $this->usersDAO->updatePassword((int)$id, $passwordHash);
        if ($success) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Mot de passe modifié']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification du mot de passe']);
        }
    }

    // Supprimer un utilisateur
    public function deleteUser(): void
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        // Vérifier le rôle de l'utilisateur
        require_once __DIR__ . '/../MiddleWare/AuthMiddleware.php';
        $token = $_COOKIE['token'] ?? '';
        if (!$token) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Token manquant.']);
            return;
        }
        try {
            $user = AuthMiddleware::verifyTokenFromCookie($token);
            if (!isset($user['role']) || $user['role'] != 1) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Accès refusé : seuls les administrateurs peuvent supprimer un utilisateur.']);
                return;
            }
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Token invalide.']);
            return;
        }
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            return;
        }
        $success = $this->usersDAO->deleteUser((int)$id);
        if ($success) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }
}
