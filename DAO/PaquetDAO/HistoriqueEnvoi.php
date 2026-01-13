<?php

class HistoriqueEnvoiDAO {

    private \PDO $pdo;

    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affiche tous les historiques d'envoi de paquet
    public function displayHistorySend(): array
    {
        try {
            $sql = "SELECT 
                            idhistorique_envoi AS idHistorySend,
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

    // Afficher l'historique d'un paquet par son id

    public function displayHistorySendById(int $itemsId): array
    {
        try {
            $sql = "SELECT 
                            idhistorique_envoi AS idHistorySend,
                            items_id AS itemsId,
                            paquet_cote AS paquetCote,
                            date_envoi AS dateEnvoi
                    FROM historique_envoi
                    WHERE items_id = :items_id
                    ORDER BY date_envoi DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':items_id', $itemsId, \PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $rows ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }
}