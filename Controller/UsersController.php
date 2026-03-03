<?php

require_once __DIR__ . '/../DAO/UsersDAO.php';

class UsersController

{
    private UsersDAO $usersDAO;

    public function __construct(UsersDAO $usersDAO)
    {
        $this->usersDAO = $usersDAO;
    }

    private function jsonResponse(int $statusCode, array $payload): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($payload);
    }

    // Afficher tous les users
    public function getAllUsers(): void
    {
        try {
            $users = $this->usersDAO->getAllUsers();

            if (empty($users)) {
                $this->jsonResponse(404, [
                    'success' => false,
                    'message' => 'Aucun utilisateur trouvé'
                ]);
                return;
            }

            $this->jsonResponse(200, [
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(500, [
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs'
            ]);
        }
    }

    // Afficher un user par ID
    public function getUserById(int $id): void
    {
        try {
            $user = $this->usersDAO->findById($id);

            if (!$user) {
                $this->jsonResponse(404, [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ]);
                return;
            }

            // ne retourne pas le mot de passe
            unset($user['password']);

            $this->jsonResponse(200, [
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(500, [
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
            $this->jsonResponse(400, [
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
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        $nom = $input['nom'] ?? null;
        $prenom = $input['prenom'] ?? null;
        $email = $input['email'] ?? null;
        $roleId = $input['roleId'] ?? null;
        if (!$id || !$nom || !$prenom || !$email || !$roleId) {
            $this->jsonResponse(400, ['success' => false, 'message' => 'Paramètres manquants']);
            return;
        }
        $success = $this->usersDAO->updateUser((int)$id, $nom, $prenom, $email, (int)$roleId);
        if ($success) {
            $this->jsonResponse(200, ['success' => true, 'message' => 'Utilisateur modifié']);
        } else {
            $this->jsonResponse(500, ['success' => false, 'message' => 'Erreur lors de la modification']);
        }
    }

    // Modifier uniquement le mot de passe d'un utilisateur
    public function updateUserPassword(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
        $password = $input['password'] ?? null;
        if (!$id || !$password) {
            $this->jsonResponse(400, ['success' => false, 'message' => 'Paramètres manquants']);
            return;
        }
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $success = $this->usersDAO->updatePassword((int)$id, $passwordHash);
        if ($success) {
            $this->jsonResponse(200, ['success' => true, 'message' => 'Mot de passe modifié']);
        } else {
            $this->jsonResponse(500, ['success' => false, 'message' => 'Erreur lors de la modification du mot de passe']);
        }
    }

    // Supprimer un utilisateur
    public function deleteUser(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $input = [];
        }
        $id = $input['id'] ?? ($_GET['id'] ?? null);
        // Vérifier le rôle de l'utilisateur
        require_once __DIR__ . '/../MiddleWare/AuthMiddleware.php';
        $token = $_COOKIE['token'] ?? '';
        if (!$token) {
            $this->jsonResponse(403, ['success' => false, 'message' => 'Token manquant.']);
            return;
        }
        try {
            $user = AuthMiddleware::verifyTokenFromCookie($token);
            if (!isset($user['role']) || $user['role'] != 1) {
                $this->jsonResponse(403, ['success' => false, 'message' => 'Accès refusé : seuls les administrateurs peuvent supprimer un utilisateur.']);
                return;
            }
        } catch (Exception $e) {
            $this->jsonResponse(403, ['success' => false, 'message' => 'Token invalide.']);
            return;
        }
        if (!$id) {
            $this->jsonResponse(400, ['success' => false, 'message' => 'ID manquant']);
            return;
        }

        $currentAdminId = isset($user['id']) ? (int) $user['id'] : 0;
        if ($currentAdminId <= 0) {
            $this->jsonResponse(403, ['success' => false, 'message' => 'Token invalide.']);
            return;
        }

        if ((int)$id === $currentAdminId) {
            $this->jsonResponse(400, ['success' => false, 'message' => 'Impossible de supprimer votre propre compte.']);
            return;
        }

        $deletedUserId = $this->usersDAO->getOrCreateDeletedUserId();
        if ($deletedUserId <= 0) {
            $this->jsonResponse(500, ['success' => false, 'message' => 'Erreur lors de la préparation de la suppression']);
            return;
        }

        if ((int)$id === $deletedUserId) {
            $this->jsonResponse(400, ['success' => false, 'message' => 'Ce compte système ne peut pas être supprimé.']);
            return;
        }

        $result = $this->usersDAO->reassignPaquetsAndDeleteUser((int)$id, $deletedUserId);
        if (!empty($result['success'])) {
            $reassignedCount = (int)($result['reassignedCount'] ?? 0);
            $this->jsonResponse(200, [
                'success' => true,
                'message' => $reassignedCount > 0
                    ? "Utilisateur supprimé. $reassignedCount paquet(s) ont été réattribué(s) à l'utilisateur supprimé."
                    : 'Utilisateur supprimé'
            ]);
        } else {
            $this->jsonResponse(500, ['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }
}
