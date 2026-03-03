<?php

class UsersDAO{
    private \PDO $pdo;

    private const DELETED_USER_EMAIL = 'utilisateur.supprime@nabu.local';
    private const DELETED_USER_FIRST_NAME = 'Utilisateur';
    private const DELETED_USER_LAST_NAME = 'supprimé';
    
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

    // Récupère (ou crée) l'utilisateur sentinel "Utilisateur supprimé" et retourne son id
    public function getOrCreateDeletedUserId(): int
    {
        try {
            $stmt = $this->pdo->prepare('SELECT idusers FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => self::DELETED_USER_EMAIL]);
            $existingId = (int) $stmt->fetchColumn();
            if ($existingId > 0) return $existingId;

            $randomPassword = bin2hex(random_bytes(16));
            $passwordHash = password_hash($randomPassword, PASSWORD_DEFAULT);
            $created = $this->createUsers(
                self::DELETED_USER_LAST_NAME,
                self::DELETED_USER_FIRST_NAME,
                self::DELETED_USER_EMAIL,
                $passwordHash,
                2
            );
            if (!$created) {
                $stmt = $this->pdo->prepare('SELECT idusers FROM users WHERE email = :email LIMIT 1');
                $stmt->execute([':email' => self::DELETED_USER_EMAIL]);
                return (int) $stmt->fetchColumn();
            }

            return (int) $this->pdo->lastInsertId();
        } catch (\Throwable $e) {
            return 0;
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
                      FROM users
                      WHERE email <> :deletedEmail";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':deletedEmail' => self::DELETED_USER_EMAIL]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    // Met à jour un utilisateur par ID
    public function updateUser(int $id, string $nom, string $prenom, string $email, int $roleId): bool
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

    // Réattribue les paquets d'un utilisateur puis supprime l'utilisateur (transaction)
    public function reassignPaquetsAndDeleteUser(int $userIdToDelete, int $reassignToUserId): array
    {
        try {
            $this->pdo->beginTransaction();

            $stmtCount = $this->pdo->prepare('SELECT COUNT(*) FROM paquet WHERE users_idusers = :id');
            $stmtCount->execute([':id' => $userIdToDelete]);
            $reassignedCount = (int) $stmtCount->fetchColumn();

            $stmtUpdate = $this->pdo->prepare('UPDATE paquet SET users_idusers = :newId WHERE users_idusers = :oldId');
            $updated = $stmtUpdate->execute([
                ':newId' => $reassignToUserId,
                ':oldId' => $userIdToDelete,
            ]);
            if (!$updated) {
                $this->pdo->rollBack();
                return ['success' => false, 'reassignedCount' => 0];
            }

            $stmtDelete = $this->pdo->prepare('DELETE FROM users WHERE idusers = :id');
            if (!$stmtDelete->execute([':id' => $userIdToDelete])) {
                $this->pdo->rollBack();
                return ['success' => false, 'reassignedCount' => 0];
            }

            $this->pdo->commit();
            return ['success' => true, 'reassignedCount' => $reassignedCount];
        } catch (\PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'reassignedCount' => 0];
        }
    }
}