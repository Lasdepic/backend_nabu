<?php
session_start();

require_once __DIR__ . "/Config/Database.php";
require_once __DIR__ . "/Model/Paquet.php";
require_once __DIR__ . "/DAO/UsersDAO.php";
require_once __DIR__ . "/Controller/Auth/RegisterController.php";
require_once __DIR__ . "/Controller/Auth/LoginController.php";
require_once __DIR__ . "/DAO/PaquetDAO/DisplayPaquetDAO.php";
require_once __DIR__ . "/Controller/PaquetController/DisplayPaquetController/DisplayPaquetController.php";
require_once __DIR__ . "/DAO/PaquetDAO/EditPaquet/CreatePaquetDAO.php";
require_once __DIR__ . "/Controller/PaquetController/EditPaquetController/CreatePaquetController.php";
require_once __DIR__ . "/DAO/PaquetDAO/EditPaquet/DeletePaquetDAO.php";
require_once __DIR__ . "/Controller/PaquetController/EditPaquetController/DeletePaquetController.php";
require_once __DIR__ . "/DAO/PaquetDAO/EditPaquet/EditPaquetDAO.php";
require_once __DIR__ . "/Controller/PaquetController/EditPaquetController/EditPaquetController.php";

$pdo = Database::getConnexion();
$userDao = new UsersDAO($pdo);
$paquetDao = new DisplayPaquetDAO($pdo);
$createPaquetDao = new CreatePaquetDAO($pdo);
$deletePaquetDao = new DeletePaquetDAO($pdo);
$editPaquetDao = new EditPaquetDAO($pdo);

$page = $_GET["page"] ?? "user";
$action = $_GET["action"] ?? null;

switch ($action) {
    // Authentification
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
        // Afficher Paquet
    case 'display-paquets':
        $displayPaquet = new DisplayPaquetController($paquetDao);
        $displayPaquet->displayPaquet();
        break;
        // Edition de paquet
    case 'create-paquet':
        $createPaquet = new CreatePaquetController($createPaquetDao);
        $createPaquet->createPaquet();
        break;
    case 'delete-paquet':
        $deletePaquet = new DeletePaquetController($deletePaquetDao);
        $deletePaquet->deletePaquet();
        break;
    case 'edit-paquet':
        $editPaquet = new EditPaquetController($editPaquetDao);
        $editPaquet->editPaquet();
        break;
}