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
                        p.cote,
                        p.folder_name AS folderName,
                        p.microfilm_image_directory AS microFilmImage,
                        p.directory_of_color_images AS imageColor,
                        p.archiving_search AS searchArchiving,
                        p.commentaire,
                        p.facile_test AS facileTest,
                        p.to_do AS toDo,
                        p.corpus_idcorpus AS corpusId,
                        c.name_corpus AS corpusName,
                        p.filed_in_sip_idfiled_in_sip AS filedSip,
                        p.users_idusers AS usersId,
                        p.date_derniere_modification AS lastmodifDate,
                        p.type_document_idtype_document AS typeDocumentId,
                        p.status_idstatus AS statusId
                    FROM paquet p
                    LEFT JOIN corpus c ON c.idcorpus = p.corpus_idcorpus
                    ORDER BY p.cote";

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
                        p.cote,
                        p.folder_name AS folderName,
                        p.microfilm_image_directory AS microFilmImage,
                        p.directory_of_color_images AS imageColor,
                        p.archiving_search AS searchArchiving,
                        p.commentaire,
                        p.facile_test AS facileTest,
                        p.to_do AS toDo,
                        p.corpus_idcorpus AS corpusId,
                        c.name_corpus AS corpusName,
                        p.filed_in_sip_idfiled_in_sip AS filedSip,
                        p.users_idusers AS usersId,
                        p.date_derniere_modification AS lastmodifDate,
                        p.type_document_idtype_document AS typeDocumentId,
                        p.status_idstatus AS statusId
                    FROM paquet p
                    LEFT JOIN corpus c ON c.idcorpus = p.corpus_idcorpus
                    WHERE p.cote = :cote
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