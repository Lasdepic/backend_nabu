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
}
