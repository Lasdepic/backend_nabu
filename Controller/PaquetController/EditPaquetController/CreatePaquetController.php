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

        // Champs sans obligation, valeurs par défaut si non fournis
        $cote = trim(isset($data['cote']) ? $data['cote'] : '');
        $folderName = trim(isset($data['folderName']) ? $data['folderName'] : '');
        $usersId = self::toInt(isset($data['usersId']) ? $data['usersId'] : null);
        $microFilmImage = isset($data['microFilmImage']) && trim($data['microFilmImage']) !== '' ? trim($data['microFilmImage']) : null;
        $imageColor = isset($data['imageColor']) && trim($data['imageColor']) !== '' ? trim($data['imageColor']) : null;
        $searchArchiving = isset($data['searchArchiving']) && trim($data['searchArchiving']) !== '' ? trim($data['searchArchiving']) : null;
        $comment = isset($data['comment']) && trim($data['comment']) !== '' ? trim($data['comment']) : null;
        $facileTest = self::toBool(isset($data['facileTest']) ? $data['facileTest'] : false) ?? false;
        $toDo = self::toBool(isset($data['toDo']) ? $data['toDo'] : false) ?? false;
        $corpusId = self::toInt(isset($data['corpusId']) ? $data['corpusId'] : null);
        $filedSip = self::toBool(isset($data['filedSip']) ? $data['filedSip'] : false) ?? false;
        $typeDocumentId = self::toInt(isset($data['typeDocumentId']) ? $data['typeDocumentId'] : null);

        // Définir le statusId par défaut à INEXISTANT si non fourni ou vide
        $statusId = self::toInt(isset($data['statusId']) ? $data['statusId'] : null);
        if ($typeDocumentId === 0 || $typeDocumentId === '') {
            $typeDocumentId = null;
        }
        // Si statusId n'est pas fourni ou vide, on cherche l'ID du statut 'INEXISTANT'
        if ($statusId === 0 || $statusId === '' || $statusId === null) {
            // Connexion à la base de données
            $db = Database::getConnexion();
            $stmt = $db->prepare("SELECT idstatus FROM status WHERE UPPER(name_status) = 'INEXISTANT' LIMIT 1");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['idstatus'])) {
                $statusId = (int)$row['idstatus'];
            } else {
                $statusId = null;
            }
        }

        $now = date('d/m/Y H:i:s');
        $paquet = new Paquet(
            $cote,
            $folderName,
            $microFilmImage,
            $imageColor,
            $searchArchiving,
            $comment,
            $facileTest,
            $toDo,
            $corpusId,
            $filedSip,
            $usersId,
            $now,
            $typeDocumentId,
            $statusId
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
