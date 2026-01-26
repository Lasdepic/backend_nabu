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
        // On supprime d'abord l'historique d'envoi lié au paquet, puis le paquet lui-même, dans une transaction
        try {
            $this->pdo->beginTransaction();

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
