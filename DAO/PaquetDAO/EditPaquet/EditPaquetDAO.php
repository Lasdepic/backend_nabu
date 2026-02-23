<?php

class EditPaquetDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Modification des éléments d'un paquet
    // Permet la modification de la cote (clé primaire)
    public function editPackage(Paquet $paquet, $oldCote): array
    {
        $sql = "
            UPDATE paquet p
            LEFT JOIN status s ON s.idstatus = :status_idstatus_check
            LEFT JOIN (
                SELECT idstatus
                FROM status
                WHERE UPPER(name_status) = 'NON_ENVOYE'
                LIMIT 1
            ) ne ON 1=1
            LEFT JOIN (
                SELECT idstatus
                FROM status
                WHERE UPPER(name_status) = 'INEXISTANT'
                LIMIT 1
            ) ie ON 1=1
            SET
                cote = :new_cote,
                folder_name = :folder_name,
                microfilm_image_directory = :microfilm_image_directory,
                directory_of_color_images = :directory_of_color_images,
                archiving_search = :archiving_search,
                commentaire = :commentaire,
                facile_test = :facile_test,
                to_do = :to_do,
                corpus_idcorpus = :corpus_idcorpus,
                filed_in_sip_idfiled_in_sip = :filed_in_sip_idfiled_in_sip,
                users_idusers = :users_idusers,
                date_derniere_modification = NOW(),
                type_document_idtype_document = :type_document_idtype_document,
                status_idstatus = CASE
                    WHEN :filed_in_sip_idfiled_in_sip = 1
                         AND (:status_idstatus_value IS NULL OR s.name_status IS NULL OR UPPER(s.name_status) = 'INEXISTANT')
                        THEN COALESCE(ne.idstatus, ie.idstatus, :status_idstatus_value)
                    ELSE :status_idstatus_value
                END
            WHERE p.cote = :old_cote
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([
                'new_cote' => $paquet->cote,
                'folder_name' => $paquet->folderName,
                'microfilm_image_directory' => $paquet->microFilmImage,
                'directory_of_color_images' => $paquet->imageColor,
                'archiving_search' => $paquet->searchArchiving,
                'commentaire' => $paquet->comment,
                'facile_test' => (int)$paquet->facileTest,
                'to_do' => (int)$paquet->toDo,
                'corpus_idcorpus' => $paquet->corpusId,
                'filed_in_sip_idfiled_in_sip' => (int)$paquet->filedSip,
                'users_idusers' => $paquet->usersId,
                'type_document_idtype_document' => $paquet->typeDocumentId,
                'status_idstatus_check' => $paquet->statusId,
                'status_idstatus_value' => $paquet->statusId,
                'old_cote' => $oldCote,
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
}