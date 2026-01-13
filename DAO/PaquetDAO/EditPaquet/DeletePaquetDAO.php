<?php

class DeletePaquetDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Supprime un paquet par son id
    
    public function deletePackageById(string $cote): array
    {
        $sql = "DELETE FROM paquet WHERE cote = :cote";

        try {
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([
                'cote' => $cote,
            ]);
            $rowCount = $stmt->rowCount();
            
            if ($success && $rowCount === 0) {
                return ['success' => false, 'error' => 'Paquet introuvable'];
            }
            
            return ['success' => $success, 'error' => null];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}