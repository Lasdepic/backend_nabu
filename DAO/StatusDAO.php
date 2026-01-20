<?php

class StatusDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }



    // Affiche tous les statuts
    public function getAllStatus(): array
    {
        try {
            $sql = "SELECT idstatus AS idStatus, name_status AS nameStatus FROM status ORDER BY idstatus ASC";
            $stmt = $this->pdo->query($sql);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $rows ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    // Affiche un statut par son id
    public function getStatusById(int $idStatus): ?array
    {
        try {
            $sql = "SELECT idstatus AS idStatus, name_status AS nameStatus FROM status WHERE idstatus = :idStatus LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':idStatus' => $idStatus]);
            $status = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $status ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

}