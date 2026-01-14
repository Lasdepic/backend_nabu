<?php

class Users
{
    public int $id;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $password;
    public int $roleId;

    public function __construct(int $id, string $nom, string $prenom, string $email, string $password, int $roleId)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->roleId = $roleId;
    }
}