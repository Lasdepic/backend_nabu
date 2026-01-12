<?php

class Status{
    public int $idStatus;
    public string $nameStatus;

    public function __construct(int $idStatus, string $nameStatus)
    {
        $this->idStatus = $idStatus;
        $this->nameStatus = $nameStatus;
    }
}