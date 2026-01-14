<?php

class EditPaquetDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Modification des éléments d'un paquet

    public function editPackage(Paquet $paquet): array
{
    $sql = "
        UPDATE paquet SET
            folder_name = :folder_name,
            microfilm_image_directory = :microfilm_image_directory,
            directory_of_color_images = :directory_of_color_images,
            archiving_search = :archiving_search,
            to_do = :to_do,
            corpus_idcorpus = :corpus_idcorpus,
            filed_in_sip_idfiled_in_sip = :filed_in_sip_idfiled_in_sip,
            users_idusers = :users_idusers,
            date_derniere_modification = NOW(),
            type_document_idtype_document = :type_document_idtype_document,
            status_idstatus = :status_idstatus
        WHERE cote = :cote
    ";

    try {
        $stmt = $this->pdo->prepare($sql);

        $success = $stmt->execute([
            'folder_name' => $paquet->folderName,
            'microfilm_image_directory' => $paquet->microFilmImage,
            'directory_of_color_images' => $paquet->imageColor,
            'archiving_search' => $paquet->searchArchiving,
            'to_do' => (int)$paquet->toDo,
            'corpus_idcorpus' => $paquet->corpusId,
            'filed_in_sip_idfiled_in_sip' => (int)$paquet->filedSip,
            'users_idusers' => $paquet->usersId,
            'type_document_idtype_document' => $paquet->typeDocumentId,
            'status_idstatus' => $paquet->statusId,
            'cote' => $paquet->cote,
        ]);
        
        $rowCount = $stmt->rowCount();
        
        if ($success && $rowCount === 0) {
            return ['success' => false, 'error' => 'Paquet introuvable'];
        }

        return ['success' => $success, 'error' => null];

    } catch (\PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

    /**
     * Modification partielle des éléments d'un paquet
     * @param string $cote Identifiant du paquet à modifier
     * @param array $fields Tableau associatif colonne => valeur (colonnes DB)
     */
    public function editPackagePartial(string $cote, array $fields): array
    {
        // Filtrer les colonnes autorisées pour éviter toute injection
        $allowed = [
            'folder_name',
            'microfilm_image_directory',
            'directory_of_color_images',
            'archiving_search',
            'to_do',
            'corpus_idcorpus',
            'filed_in_sip_idfiled_in_sip',
            'users_idusers',
            'type_document_idtype_document',
            'status_idstatus',
        ];

        $setParts = [];
        $params = [];

        foreach ($fields as $col => $val) {
            if (!in_array($col, $allowed, true)) {
                continue;
            }
            $setParts[] = "$col = :$col";
            $params[$col] = $val;
        }

        if (empty($setParts)) {
            return ['success' => false, 'error' => 'Aucun champ valide à modifier'];
        }

        // Ajouter la date de dernière modification
        $setSql = implode(", ", $setParts) . ", date_derniere_modification = NOW()";
        $sql = "UPDATE paquet SET $setSql WHERE cote = :cote";

        try {
            $stmt = $this->pdo->prepare($sql);
            $params['cote'] = $cote;
            $success = $stmt->execute($params);
            $rowCount = $stmt->rowCount();

            if ($success && $rowCount === 0) {
                return ['success' => false, 'error' => 'Paquet introuvable'];
            }

            return ['success' => $success, 'error' => null];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}