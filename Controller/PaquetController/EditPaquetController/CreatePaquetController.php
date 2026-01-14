<?php

require_once __DIR__ . '/../../../Config/Database.php';
require_once __DIR__ . '/../../../Model/Paquet.php';
require_once __DIR__ . '/../../../DAO/PaquetDAO/EditPaquet/CreatePaquetDAO.php';

class CreatePaquetController
{
    private CreatePaquetDAO $paquetDao;

    public function __construct(CreatePaquetDAO $paquetDao)
    {
        $this->paquetDao = $paquetDao;
    }

    public function createPaquet(): void
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

        $isJson = stripos(isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '', 'application/json') !== false;
        $data = $isJson ? json_decode(file_get_contents('php://input'), true) : $_POST;

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Données invalides'
            ]);
            return;
        }

        // Champs obligatoire a remplir pour crée un paquet
        $cote = trim(isset($data['cote']) ? $data['cote'] : '');
        $folderName = trim(isset($data['folderName']) ? $data['folderName'] : '');
        $usersId = self::toInt(isset($data['usersId']) ? $data['usersId'] : null);

        // Champs optionnels avec valeurs par défaut
        $microFilmImage = trim(isset($data['microFilmImage']) ? $data['microFilmImage'] : '');
        $imageColor = trim(isset($data['imageColor']) ? $data['imageColor'] : '');
        $searchArchiving = trim(isset($data['searchArchiving']) ? $data['searchArchiving'] : '');
        $comment = trim(isset($data['comment']) ? $data['comment'] : '');

        $toDo = self::toBool(isset($data['toDo']) ? $data['toDo'] : false);
        $corpusId = self::toInt(isset($data['corpusId']) ? $data['corpusId'] : null);
        $filedSip = self::toBool(isset($data['filedSip']) ? $data['filedSip'] : false);
        $typeDocumentId = self::toInt(isset($data['typeDocumentId']) ? $data['typeDocumentId'] : null);
        $statusId = self::toInt(isset($data['statusId']) ? $data['statusId'] : null);

        // Validation des champs obligatoires uniquement
        $missing = [];
        if ($cote === '') $missing[] = 'cote';
        if ($folderName === '') $missing[] = 'folderName';
        if ($usersId === null) $missing[] = 'usersId';

        if (!empty($missing)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Champs manquants ou invalides',
                'fields' => $missing
            ]);
            return;
        }

        $now = date('d/m/Y H:i:s');
        $paquet = new Paquet(
            $cote,
            $folderName,
            $microFilmImage,
            $imageColor,
            $searchArchiving,
            $comment,
            (bool)$toDo,
            (int)$corpusId,
            (bool)$filedSip,
            (int)$usersId,
            $now,
            (int)$typeDocumentId,
            (int)$statusId
        );

        $result = $this->paquetDao->createPackage($paquet);

        if (!$result['success']) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la création du paquet',
                'error' => $result['error']
            ]);
            return;
        }

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Paquet créé avec succès',
            'data' => [
                'cote' => $cote
            ]
        ]);
    }

    private static function toBool($value): ?bool
    {
        if (is_bool($value)) return $value;
        if (is_int($value)) return $value === 1 ? true : ($value === 0 ? false : null);
        if (is_string($value)) {
            $v = strtolower(trim($value));
            if ($v === 'true' || $v === '1') return true;
            if ($v === 'false' || $v === '0') return false;
        }
        return null;
    }

    private static function toInt($value): ?int
    {
        if ($value === null || $value === '') return null;
        if (is_int($value)) return $value;
        if (is_numeric($value)) return (int)$value;
        return null;
    }
}


