<?php

class TypeDocumentDAO
{
	private \PDO $pdo;

	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}


	// Récupère tous les types de documents
	public function getAllTypeDocuments(): array
	{
		$sql = "SELECT idType_Document, name_Document FROM type_document";
		try {
			$stmt = $this->pdo->query($sql);
			$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return ['success' => true, 'data' => $data, 'error' => null];
		} catch (\PDOException $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}

	// Récupère un type de document par son ID
	public function getTypeDocumentById(int $id): array
	{
		$sql = "SELECT idType_Document, name_Document FROM type_document WHERE idType_Document = :id";
		try {
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(['id' => $id]);
			$data = $stmt->fetch(\PDO::FETCH_ASSOC);
			if (!$data) {
				return ['success' => false, 'error' => 'TypeDocument introuvable'];
			}
			return ['success' => true, 'data' => $data, 'error' => null];
		} catch (\PDOException $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}
}

