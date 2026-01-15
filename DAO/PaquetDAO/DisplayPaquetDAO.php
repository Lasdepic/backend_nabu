<?php

class DisplayPaquetDAO{

    private \PDO $pdo;
    
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

     // Affiche tout les paquets

    public function displayAllPackages(): array
    {
        try {
            $sql = "SELECT 
                        cote,
                        folder_name AS folderName,
                        microfilm_image_directory AS microFilmImage,
                        directory_of_color_images AS imageColor,
                        archiving_search AS searchArchiving,
                        commentaire,
                        facile_test AS facileTest,
                        to_do AS toDo,
                        corpus_idcorpus AS corpusId,
                        filed_in_sip_idfiled_in_sip AS filedSip,
                        users_idusers AS usersId,
                        date_derniere_modification AS lastmodifDate,
                        type_document_idtype_document AS typeDocumentId,
                        status_idstatus AS statusId
                    FROM paquet
                    ORDER BY cote";

            $stmt = $this->pdo->query($sql);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $rows ?: [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    // Affiche un paquet par son id

    public function displayPackageById(string $cote): ?array
    {
        try {
            $sql = "SELECT 
                        cote,
                        folder_name AS folderName,
                        microfilm_image_directory AS microFilmImage,
                        directory_of_color_images AS imageColor,
                        archiving_search AS searchArchiving,
                        commentaire,
                        facile_test AS facileTest,
                        to_do AS toDo,
                        corpus_idcorpus AS corpusId,
                        filed_in_sip_idfiled_in_sip AS filedSip,
                        users_idusers AS usersId,
                        date_derniere_modification AS lastmodifDate,
                        type_document_idtype_document AS typeDocumentId,
                        status_idstatus AS statusId
                    FROM paquet
                    WHERE cote = :cote
                    LIMIT 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':cote' => $cote]);
            $paquet = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $paquet ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }
}