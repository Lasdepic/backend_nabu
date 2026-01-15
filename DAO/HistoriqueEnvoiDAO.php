<?php

class HistoriqueEnvoiDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    // Affiche tous les envois d'un paquet par sa cote

    public function displayHistorySendByPaquetCote(string $paquetCote): array
    {
        try {
            $sql = "SELECT 
                        idhistorique_envoi AS idHistoriqueEnvoi,
                        items_id AS itemsId,
                        paquet_cote AS paquetCote,
                        date_envoi AS dateEnvoi
                    FROM historique_envoi
                    WHERE paquet_cote = :paquetCote
                    ORDER BY date_envoi DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':paquetCote' => $paquetCote]);
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