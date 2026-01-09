<?php

class Users
{
    public int $id;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $password;
    public int $roleId;

    public function __construct($id, $nom, $prenom, $email, $password, $roleId)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->roleId = $roleId;
    }
}