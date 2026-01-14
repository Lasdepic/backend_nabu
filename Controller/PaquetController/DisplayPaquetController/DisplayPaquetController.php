<?php

require_once __DIR__ . '/../../../DAO/PaquetDAO/DisplayPaquetDAO.php';

class DisplayPaquetController
{
    private DisplayPaquetDAO $paquetDao;

    public function __construct(DisplayPaquetDAO $paquetDao)
    {
        $this->paquetDao = $paquetDao;
    }

    public function displayPaquet(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Methode non autorisee'
            ]);
            return;
        }

        $paquets = $this->paquetDao->displayAllPackages();

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $paquets
        ]);
    }

    public function displayPaquetByCote(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Methode non autorisee'
            ]);
            return;
        }

        $cote = $_GET['cote'] ?? null;

        if (!$cote || trim($cote) === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Parametre cote manquant ou vide'
            ]);
            return;
        }

        $paquet = $this->paquetDao->displayPackageById(trim($cote));

        if ($paquet === null) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Paquet non trouve'
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $paquet
        ]);
    }
}
