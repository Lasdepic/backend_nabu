<?php 

class Corpus{
    public int $idCorpus;
    public string $nameCorpus;
    public string $descriptionCorpus;

    public function __construct(int $idCorpus, string $nameCorpus, string $descriptionCorpus)
    {
        $this->idCorpus = $idCorpus;
        $this->nameCorpus = $nameCorpus;
        $this->descriptionCorpus = $descriptionCorpus;
    }
}