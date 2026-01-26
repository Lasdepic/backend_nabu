<?php

class DeletePaquetDAO
{

    private \PDO $pdo;

    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Supprime un paquet par son id

    public function deletePackageById(string $cote): array
    {
        try {
            $this->pdo->beginTransaction();

            $sqlCheck = "SELECT s.name_status FROM paquet p JOIN status s ON p.status_idstatus = s.idstatus WHERE p.cote = :cote LIMIT 1";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute(['cote' => $cote]);
            $row = $stmtCheck->fetch(\PDO::FETCH_ASSOC);
            if (!$row) {
                $this->pdo->rollBack();
                return ['success' => false, 'error' => 'Paquet introuvable'];
            }
            if (strtoupper($row['name_status']) === 'ENVOI_OK') {
                $this->pdo->rollBack();
                return ['success' => false, 'error' => 'Impossible de supprimer un paquet avec le statut ENVOI_OK'];
            }

            // Supprimer l'historique d'envoi lié au paquet
            $sqlHistorique = "DELETE FROM historique_envoi WHERE paquet_cote = :cote";
            $stmtHistorique = $this->pdo->prepare($sqlHistorique);
            $stmtHistorique->execute(['cote' => $cote]);

            $sqlPaquet = "DELETE FROM paquet WHERE cote = :cote";
            $stmtPaquet = $this->pdo->prepare($sqlPaquet);
            $stmtPaquet->execute(['cote' => $cote]);
            $rowCount = $stmtPaquet->rowCount();

            if ($rowCount === 0) {
                $this->pdo->rollBack();
                return ['success' => false, 'error' => 'Paquet introuvable'];
            }

            $this->pdo->commit();
            return ['success' => true, 'error' => null];
        } catch (\PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
