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

		if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

		$cote = trim($data['cote'] ?? '');
		$folderName = trim($data['folderName'] ?? '');
		$microFilmImage = trim($data['microFilmImage'] ?? '');
		$imageColor = trim($data['imageColor'] ?? '');
		$searchArchiving = trim($data['searchArchiving'] ?? '');
		$comment = trim($data['comment'] ?? '');

		$toDo = self::toBool($data['toDo'] ?? null);
		$corpusId = self::toInt($data['corpusId'] ?? null);
		$filedSip = self::toBool($data['filedSip'] ?? null);
		$usersId = self::toInt($data['usersId'] ?? null);
		$typeDocumentId = self::toInt($data['typeDocumentId'] ?? null);
		$statusId = self::toInt($data['statusId'] ?? null);

		$missing = [];
		if ($cote === '') $missing[] = 'cote';
		if ($folderName === '') $missing[] = 'folderName';
		if ($microFilmImage === '') $missing[] = 'microFilmImage';
		if ($imageColor === '') $missing[] = 'imageColor';
		if ($searchArchiving === '') $missing[] = 'searchArchiving';
		if ($toDo === null) $missing[] = 'toDo';
		if ($corpusId === null) $missing[] = 'corpusId';
		if ($filedSip === null) $missing[] = 'filedSip';
		if ($usersId === null) $missing[] = 'usersId';
		if ($typeDocumentId === null) $missing[] = 'typeDocumentId';
		if ($statusId === null) $missing[] = 'statusId';

		if (!empty($missing)) {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'Champs manquants ou invalides',
				'fields' => $missing
			]);
			return;
		}

		$now = date('Y-m-d H:i:s');
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

		$result = $this->paquetDao->editPackage($paquet);

		if (!$result['success']) {
			http_response_code($result['error'] === 'Paquet introuvable' ? 404 : 500);
			echo json_encode([
				'success' => false,
				'message' => $result['error'] ?? 'Erreur lors de la modification du paquet'
			]);
			return;
		}

		http_response_code(200);
		echo json_encode([
			'success' => true,
			'message' => 'Paquet modifié avec succès',
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
