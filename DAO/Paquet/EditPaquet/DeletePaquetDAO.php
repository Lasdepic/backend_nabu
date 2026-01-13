<?php

class DeletePaquetDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Supprime un paquet par son id
    
    public function deletePackageById(string $cote): bool
    {
        $sql = "DELETE FROM paquet WHERE cote = :cote";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'cote' => $cote,
            ]);
        } catch (\PDOException) {
            return false;
        }
    }
}