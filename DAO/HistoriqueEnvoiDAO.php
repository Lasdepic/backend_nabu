<?php

class HistoriqueEnvoiDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affiche tous les historiques d'envoi

    public function displayAllHistorySend(): array
    {
        try {
            $sql = "SELECT 
                        idhistorique_envoi AS idHistoriqueEnvoi,
                        items_id AS itemsId,
                        paquet_cote AS paquetCote,
                        date_envoi AS dateEnvoi
                    FROM historique_envoi
                    ORDER BY date_envoi DESC";

            $stmt = $this->pdo->query($sql);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $rows ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    // Affiche un historique d'envoi par son id

    public function displayHistorySendById(int $idHistoriqueEnvoi): ?array
    {
        try {
            $sql = "SELECT 
                        idhistorique_envoi AS idHistoriqueEnvoi,
                        items_id AS itemsId,
                        paquet_cote AS paquetCote,
                        date_envoi AS dateEnvoi
                    FROM historique_envoi
                    WHERE idhistorique_envoi = :idHistoriqueEnvoi
                    LIMIT 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':idHistoriqueEnvoi' => $idHistoriqueEnvoi]);
            $historique = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $historique ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }
}