<?php

require_once __DIR__ . '/../DAO/CorpusDAO.php';

class CorpusController
{
    private CorpusDAO $corpusDAO;

    public function __construct(CorpusDAO $corpusDAO)
    {
        $this->corpusDAO = $corpusDAO;
    }

    // Créer un corpus
    public function createCorpus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Méthode non autorisée'
            ]);
            return;
        }

        $isJson = stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;
        $data = $isJson ? json_decode(file_get_contents('php://input'), true) : $_POST;

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Données invalides'
            ]);
            return;
        }

        $nameCorpus = trim($data['nameCorpus'] ?? '');
        $descriptionCorpus = trim($data['descriptionCorpus'] ?? '');

        if ($nameCorpus === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Champ nameCorpus manquant ou vide'
            ]);
            return;
        }

        $result = $this->corpusDAO->create($nameCorpus, $descriptionCorpus !== '' ? $descriptionCorpus : null);

        if (!$result['success']) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la création du corpus',
                'error' => $result['error'] ?? null
            ]);
            return;
        }

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Corpus créé avec succès',
            'data' => [
                'idcorpus' => $result['id'] ?? null,
                'nameCorpus' => $nameCorpus
            ]
        ]);
    }

    // Supprimer un corpus par id
    public function deleteCorpus(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Méthode non autorisée'
            ]);
            return;
        }

        // Accepte l'id via query (?id=) ou JSON
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if ($id === null || $id === 0) {
            $isJson = stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;
            $data = $isJson ? json_decode(file_get_contents('php://input'), true) : [];
            if (is_array($data) && isset($data['id'])) {
                $id = (int)$data['id'];
            }
        }

        if ($id === null || $id === 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Paramètre id manquant ou invalide'
            ]);
            return;
        }

        $result = $this->corpusDAO->deleteById($id);

        if (!$result['success']) {
            http_response_code(($result['error'] ?? '') === 'Corpus introuvable' ? 404 : 500);
            echo json_encode([
                'success' => false,
                'message' => $result['error'] ?? 'Erreur lors de la suppression du corpus'
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Corpus supprimé avec succès',
            'data' => [
                'idcorpus' => $id
            ]
        ]);
    }
}
