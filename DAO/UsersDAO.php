<?php

class UsersDAO{
    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Création de compte
    public function createUsers(string $nom, string $prenom, string $email, string $passwordHash, int $roleId): bool
    {
        $sql = "INSERT INTO users (last_name, first_name, email, password, role_idrole)
                VALUES (:nom, :prenom, :email, :password, :role_id)";
        
        $stmt = $this->pdo->prepare($sql);

        try {
            return $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':password' => $passwordHash,
                ':role_id' => $roleId
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Vérification d'un email 
    public function emailExists(string $email): bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            return (bool) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Récupère un user par email
    public function findByEmail(string $email): ?array
    {
        try {
            $query = "SELECT 
                        idusers AS id,
                        last_name AS nom,
                        first_name AS prenom,
                        email,
                        password,
                        role_idrole AS roleId
                      FROM users 
                      WHERE email = :email 
                      LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    // Récupere un user par ID
    public function findById(int $id): ?array
    {
        try {
            $query = "SELECT idusers AS id, last_name AS nom, first_name AS prenom, email, password, role_idrole AS roleId FROM users WHERE idusers = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    // Récupère tous les users
    public function getAllUsers(): array
    {
        try {
            $query = "SELECT 
                        idusers AS id,
                        last_name AS nom,
                        first_name AS prenom,
                        email,
                        role_idrole AS roleId
                      FROM users";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    // Met à jour un utilisateur par ID
    public function updateUser(int $id, string $nom, string $prenom, string $email, ?string $passwordHash, int $roleId): bool
    {
        try {
            $sql = "UPDATE users SET last_name = :nom, first_name = :prenom, email = :email, role_idrole = :roleId WHERE idusers = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':roleId' => $roleId,
                ':id' => $id
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Met à jour uniquement le mot de passe d'un utilisateur
    public function updatePassword(int $id, string $passwordHash): bool
    {
        try {
            $sql = "UPDATE users SET password = :password WHERE idusers = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':password' => $passwordHash,
                ':id' => $id
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Supprime un utilisateur par ID
    public function deleteUser(int $id): bool
    {
        $sql = "DELETE FROM users WHERE idusers = :id";
        $stmt = $this->pdo->prepare($sql);
        try {
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
}