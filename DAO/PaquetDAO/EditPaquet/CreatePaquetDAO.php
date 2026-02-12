<?php

class CreatePaquetDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createPackage(Paquet $paquet): array
{
    $sql = "
        INSERT INTO paquet (
            cote,
            folder_name,
            microfilm_image_directory,
            directory_of_color_images,
            archiving_search,
            commentaire,
            facile_test,
            to_do,
            corpus_idcorpus,
            filed_in_sip_idfiled_in_sip,
            users_idusers,
            date_derniere_modification,
            type_document_idtype_document,
            status_idstatus
        ) VALUES (
            :cote,
            :folder_name,
            :microfilm_image_directory,
            :directory_of_color_images,
            :archiving_search,
            :commentaire,
            :facile_test,
            :to_do,
            :corpus_idcorpus,
            :filed_in_sip_idfiled_in_sip,
            :users_idusers,
            NOW(),
            :type_document_idtype_document,
            COALESCE(
                :status_idstatus,
                (SELECT idstatus FROM status WHERE UPPER(name_status) = 'INEXISTANT' LIMIT 1)
            )
        )
    ";

    try {
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            'cote' => $paquet->cote,
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
            'status_idstatus' => $paquet->statusId,
        ]);
        return ['success' => $success, 'error' => null];

    } catch (\PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
}