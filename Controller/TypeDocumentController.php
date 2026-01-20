<?php

require_once __DIR__ . '/../DAO/TypeDocumentDAO.php';


class TypeDocumentController
{
    private TypeDocumentDAO $typeDocumentDAO;

    public function __construct(TypeDocumentDAO $typeDocumentDAO)
    {
        $this->typeDocumentDAO = $typeDocumentDAO;
    }

    // Afficher tous les types de documents
    public function displayAllTypeDocuments(): void
    {
        header('Content-Type: application/json');
        $result = $this->typeDocumentDAO->getAllTypeDocuments();
        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $result['data']
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $result['error']
            ]);
        }
    }

    // Afficher un type de document par son ID
    public function displayTypeDocumentById(int $idTypeDocument): void
    {
        header('Content-Type: application/json');
        $result = $this->typeDocumentDAO->getTypeDocumentById($idTypeDocument);
        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $result['data']
            ]);
        } else {
            http_response_code($result['error'] === 'TypeDocument introuvable' ? 404 : 500);
            echo json_encode([
                'success' => false,
                'message' => $result['error']
            ]);
        }
    }

    // Gère la requête GET pour un type de document (par ID)
    public function handleGetTypeDocumentRequest(): void
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
        $this->displayTypeDocumentById((int)$id);
    }
}
