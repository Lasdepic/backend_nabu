<?php
require_once __DIR__ . '/../DAO/StatusDAO.php';

class StatusController {
    private StatusDAO $statusDao;

    public function __construct(StatusDAO $statusDao)
    {
        $this->statusDao = $statusDao;
    }

    // GET /?action=get-status-all
    public function getAllStatus()
    {
        $status = $this->statusDao->getAllStatus();
        header('Content-Type: application/json');
        echo json_encode($status);
    }

    // GET /?action=get-status&id=1
    public function getStatusById()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID manquant ou invalide']);
            return;
        }
        $status = $this->statusDao->getStatusById($id);
        if ($status) {
            header('Content-Type: application/json');
            echo json_encode($status);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Statut non trouvé']);
        }
    }
}
