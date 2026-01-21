<?php
class Paquet{
    public string $cote;
    public string $folderName;
    public string $microFilmImage;
    public string $imageColor;
    public string $searchArchiving;
    public string $comment;
    public bool $facileTest;
    public bool $toDo;
    public ?int $corpusId;
    public bool $filedSip;
    public int $usersId;
    public string $lastmodifDate;
    public int $typeDocumentId;
    public int $statusId;

    public function __construct(string $cote, string $folderName, string $microFilmImage, string $imageColor, string $searchArchiving, string $comment, bool $facileTest, bool $toDo, ?int $corpusId, bool $filedSip, int $usersId, string $lastmodifDate, int $typeDocumentId, int $statusId)
    {
        $this->cote = $cote;
        $this->folderName = $folderName;
        $this->microFilmImage = $microFilmImage;
        $this->imageColor = $imageColor;
        $this->searchArchiving = $searchArchiving;
        $this->comment = $comment;
        $this->facileTest = $facileTest;
        $this->toDo = $toDo;
        $this->corpusId = $corpusId;
        $this->filedSip = $filedSip;
        $this->usersId = $usersId;
        $this->lastmodifDate = $lastmodifDate;
        $this->typeDocumentId = $typeDocumentId;
        $this->statusId = $statusId;
    }
}