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

        $paquetCote = $_GET['paquet_cote'] ?? $_GET['paquetCote'] ?? null;

        if (!$paquetCote) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Parametre paquet_cote manquant'
            ]);
            return;
        }

        try {
            $historiques = $this->historiqueEnvoiDAO->displayHistorySendByPaquetCote($paquetCote);

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

    // Crée un historique d'envoi (POST)
    public function createHistorySend(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }

        $isJson = stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;
        $data = $isJson ? json_decode(file_get_contents('php://input'), true) : $_POST;

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        $itemsId = trim((string)($data['itemsId'] ?? $data['items_id'] ?? ''));
        $paquetCote = trim((string)($data['paquetCote'] ?? $data['paquet_cote'] ?? ''));

        if ($itemsId === '' || $paquetCote === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Champs itemsId et/ou paquetCote manquants'
            ]);
            return;
        }

        $result = $this->historiqueEnvoiDAO->createHistorySend($itemsId, $paquetCote);

        if (!($result['success'] ?? false)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => "Erreur lors de l'enregistrement de l'historique d'envoi",
                'error' => $result['error'] ?? null
            ]);
            return;
        }

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'id' => $result['id'] ?? null
        ]);
    }
}
