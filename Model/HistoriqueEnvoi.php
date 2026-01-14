<?php

class HistoriqueEnvoi{
    public int $idHistorySend;
    public string $itemsId;
    public string $paquetCote;
    public string $dateEnvoi;

    public function __construct(int $idHistorySend, string $itemsId, string $paquetCote, string $dateEnvoi)
    {
        $this->idHistorySend = $idHistorySend;
        $this->itemsId = $itemsId;
        $this->paquetCote = $paquetCote;
        $this->dateEnvoi = $dateEnvoi;
    }
}