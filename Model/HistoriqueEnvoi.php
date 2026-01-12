<?php

class HistoriqueEnvoi{
    public int $idHistorySend;
    public string $itemsId;
    public string $paquetCote;

    public function __construct(int $idHistorySend, string $itemsId, string $paquetCote)
    {
        $this->idHistorySend = $idHistorySend;
        $this->itemsId = $itemsId;
        $this->paquetCote = $paquetCote;
    }
}