<?php
session_start();

require_once __DIR__ . "/Config/Database.php";
require_once __DIR__ . "/DAO/UsersDAO.php";
require_once __DIR__ . "/Controller/Auth/RegisterController.php";
require_once __DIR__ . "/Controller/Auth/LoginController.php";

$pdo = Database::getConnexion();
$userDao = new UsersDAO($pdo);

$page = $_GET["page"] ?? "user";
$action = $_GET["action"] ?? null;

switch ($action) {
    case 'register':
        $register = new RegisterController($userDao);
        $register->register();
        break;
    case 'login':
        $login = new LoginController($userDao);
        $login->login();
        break;
    case 'logout':
        $login = new LoginController($userDao);
        $login->logout();
        break;
}