<?php

class CorpusDAO
{
	private \PDO $pdo;

	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	// Crée un corpus et retourne l'id inséré
	public function create(string $nameCorpus, ?string $descriptionCorpus = null): array
	{
		$sql = "INSERT INTO corpus (name_corpus, desciption_corpus) VALUES (:name_corpus, :desciption_corpus)";

		try {
			$stmt = $this->pdo->prepare($sql);
			$success = $stmt->execute([
				'name_corpus' => $nameCorpus,
				'desciption_corpus' => $descriptionCorpus,
			]);
			if (!$success) {
				return ['success' => false, 'error' => 'Insertion échouée'];
			}

			$id = (int)$this->pdo->lastInsertId();
			return ['success' => true, 'id' => $id, 'error' => null];
		} catch (\PDOException $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}

	public function editPaquetById(int $idCorpus, Corpus $corpus): array
	{
		$sql = "UPDATE corpus SET name_corpus = :name_corpus, desciption_corpus = :desciption_corpus WHERE idcorpus = :id";

		try {
			$stmt = $this->pdo->prepare($sql);
			$success = $stmt->execute([
				'name_corpus' => $corpus->nameCorpus,
				'desciption_corpus' => $corpus->descriptionCorpus,
				'id' => $idCorpus,
			]);
			$rowCount = $stmt->rowCount();

			if ($success && $rowCount === 0) {
				return ['success' => false, 'error' => 'Corpus introuvable'];
			}

			return ['success' => $success, 'error' => null];
		} catch (\PDOException $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}

	// Supprime un corpus par son id
	public function deleteById(int $idCorpus): array
	{
		$sql = "DELETE FROM corpus WHERE idcorpus = :id";

		try {
			$stmt = $this->pdo->prepare($sql);
			$success = $stmt->execute(['id' => $idCorpus]);
			$rowCount = $stmt->rowCount();

			if ($success && $rowCount === 0) {
				return ['success' => false, 'error' => 'Corpus introuvable'];
			}

			return ['success' => $success, 'error' => null];
		} catch (\PDOException $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}

	public function displayAllCorpus(){
		$sql = "SELECT idcorpus, name_corpus, desciption_corpus FROM corpus";

		try {
			$stmt = $this->pdo->query($sql);
			$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return ['success' => true, 'data' => $data, 'error' => null];
		} catch (\PDOException $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}

	public function displayOneCorpus(int $idCorpus){
		$sql = "SELECT idcorpus, name_corpus, desciption_corpus FROM corpus WHERE idcorpus = :id";

		try {
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(['id' => $idCorpus]);
			$data = $stmt->fetch(\PDO::FETCH_ASSOC);

			if (!$data) {
				return ['success' => false, 'error' => 'Corpus introuvable'];
			}

			return ['success' => true, 'data' => $data, 'error' => null];
		} catch (\PDOException $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}
}

