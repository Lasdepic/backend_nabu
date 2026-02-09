<?php

require_once __DIR__ . '/../../../Config/Database.php';
require_once __DIR__ . '/../../../DAO/PaquetDAO/EditPaquet/DeletePaquetDAO.php';

class DeletePaquetController
{
	private DeletePaquetDAO $paquetDao;

	public function __construct(DeletePaquetDAO $paquetDao)
	{
		$this->paquetDao = $paquetDao;
	}

	public function deletePaquet(): void
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

		$cote = isset($_GET['cote']) ? trim($_GET['cote']) : null;

		if ($cote === null || $cote === '') {
			http_response_code(400);
			echo json_encode([
				'success' => false,
				'message' => 'Paramètre cote manquant ou vide'
			]);
			return;
		}


		$result = $this->paquetDao->deletePackageById($cote);

		if (!$result['success']) {
			if ($result['error'] === 'Paquet introuvable') {
				http_response_code(404);
			} elseif ($result['error'] === 'Impossible de supprimer un paquet avec le statut envoyée') {
				http_response_code(403);
			} else {
				http_response_code(500);
			}
			echo json_encode([
				'success' => false,
				'message' => $result['error'] ?? 'Erreur lors de la suppression du paquet'
			]);
			return;
		}

		http_response_code(200);
		echo json_encode([
			'success' => true,
			'message' => 'Paquet supprimé avec succès',
			'data' => [
				'cote' => $cote
			]
		]);
	}
}
