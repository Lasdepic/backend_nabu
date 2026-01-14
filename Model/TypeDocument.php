<?php

class TypeDocument{
    public int $idTypeDocument;
    public string $nameDocument;

    public function __construct(int $idTypeDocument, string $nameDocument)
    {
        $this->idTypeDocument = $idTypeDocument;
        $this->nameDocument = $nameDocument;
    }
}