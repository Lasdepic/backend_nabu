<?php

require_once __DIR__ . '/../../../Config/Database.php';
require_once __DIR__ . '/../../../Model/Paquet.php';
require_once __DIR__ . '/../../../DAO/PaquetDAO/EditPaquet/EditPaquetDAO.php';

class EditPaquetController
{
    private EditPaquetDAO $paquetDao;

    public function __construct(EditPaquetDAO $paquetDao)
    {
        $this->paquetDao = $paquetDao;
    }

    public function editPaquet(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Vérifie la méthode
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['PUT', 'POST'], true)) {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }

        // Récupération des données
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: $_POST;

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        // champs de modification
        $cote = isset($data['cote']) ? trim($data['cote']) : null;
        $oldCote = isset($data['oldCote']) ? trim($data['oldCote']) : $cote;
        $folderName = isset($data['folderName']) ? trim($data['folderName']) : null;
        $microFilmImage = isset($data['microFilmImage']) ? trim($data['microFilmImage']) : null;
        $imageColor = isset($data['imageColor']) ? trim($data['imageColor']) : null;
        $searchArchiving = isset($data['searchArchiving']) ? trim($data['searchArchiving']) : null;
        $comment = isset($data['comment']) ? trim($data['comment']) : '';

        $facileTest = isset($data['facileTest']) ? self::toBool($data['facileTest']) : null;
        $toDo = isset($data['toDo']) ? self::toBool($data['toDo']) : null;
        $corpusId = isset($data['corpusId']) ? self::toInt($data['corpusId']) : null;
        $filedSip = isset($data['filedSip']) ? self::toBool($data['filedSip']) : null;
        $usersId = isset($data['usersId']) ? self::toInt($data['usersId']) : null;
        $typeDocumentId = isset($data['typeDocumentId']) ? self::toInt($data['typeDocumentId']) : null;
        $statusId = isset($data['statusId']) ? self::toInt($data['statusId']) : null;

        $now = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $formattedDate = $now->format('d/m/Y H:i:s');

        //  paquet a jour
        $paquet = new Paquet(
            $cote,
            $folderName,
            $microFilmImage,
            $imageColor,
            $searchArchiving,
            $comment,
            $facileTest ?? false,
            $toDo ?? false,
            $corpusId,
            $filedSip ?? false,
            $usersId ?? 0,
            $formattedDate,
            $typeDocumentId ?? 0,
            $statusId ?? 0
        );

        $result = $this->paquetDao->editPackage($paquet, $oldCote);

        if (!$result['success']) {
            http_response_code($result['error'] === 'Paquet introuvable' ? 404 : 500);
            echo json_encode([
                'success' => false,
                'message' => $result['error'] ?? 'Erreur lors de la modification du paquet'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Paquet modifié avec succès',
            'data' => ['cote' => $cote]
        ]);
    }

    private static function toBool($value): ?bool
    {
        if (in_array($value, [true, 'true', 1, '1'], true)) return true;
        if (in_array($value, [false, 'false', 0, '0'], true)) return false;
        return null;
    }

    private static function toInt($value): ?int
    {
        return is_numeric($value) ? (int)$value : null;
    }
}
