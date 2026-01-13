<?php

class EditPaquetDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Modification des éléments d'un paquet

    public function editPackage(Paquet $paquet): bool
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

        return $stmt->execute([
            'folder_name' => $paquet->folderName,
            'microfilm_image_directory' => $paquet->microFilmImage,
            'directory_of_color_images' => $paquet->imageColor,
            'archiving_search' => $paquet->searchArchiving,
            'to_do' => $paquet->toDo,
            'corpus_idcorpus' => $paquet->corpusId,
            'filed_in_sip_idfiled_in_sip' => $paquet->filedSip,
            'users_idusers' => $paquet->usersId,
            'type_document_idtype_document' => $paquet->typeDocumentId,
            'status_idstatus' => $paquet->statusId,
            'cote' => $paquet->cote,
        ]);

    } catch (\PDOException) {
        return false;
    }
}
}