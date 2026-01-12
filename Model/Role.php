<?php
class Role{
    public int $id;
    public string $role;

    public function __construct(int $id, string $role)
    {
        $this->id = $id;
        $this->role = $role;
    }
}