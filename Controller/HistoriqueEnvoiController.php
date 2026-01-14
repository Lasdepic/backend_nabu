<?php

require_once __DIR__ . '/../DAO/HistoriqueEnvoiDAO.php';

class HistoriqueEnvoiController
{
    private HistoriqueEnvoiDAO $historiqueEnvoiDAO;

    public function __construct(HistoriqueEnvoiDAO $historiqueEnvoiDAO)
    {
        $this->historiqueEnvoiDAO = $historiqueEnvoiDAO;
    }

    // Afficher tous les historiques d'envoi
    public function displayAllHistorySend(): void
    {
        header('Content-Type: application/json');

        try {
            $historiques = $this->historiqueEnvoiDAO->displayAllHistorySend();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $historiques
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => "Erreur lors de la récupération de l'historique d'envoi"
            ]);
        }
    }

    // Afficher un historique d'envoi par son ID
    public function displayHistorySendById(int $idHistoriqueEnvoi): void
    {
        header('Content-Type: application/json');

        try {
            $historique = $this->historiqueEnvoiDAO->displayHistorySendById($idHistoriqueEnvoi);

            if (!$historique) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => "Historique d'envoi non trouvé"
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $historique
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => "Erreur lors de la récupération de l'historique d'envoi"
            ]);
        }
    }

    // Gère la requête GET pour l'historique (par ID)
    public function handleGetHistoryRequest(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID manquant'
            ]);
            return;
        }

        $this->displayHistorySendById((int)$id);
    }
}
