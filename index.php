
<?php
require_once __DIR__ . '/Config/Cors.php';
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Config/Database.php';
require_once __DIR__ . '/Controller/Auth/AuthController.php';
require_once __DIR__ . '/DAO/UsersDAO.php';
require_once __DIR__ . '/Controller/UsersController.php';
require_once __DIR__ . '/DAO/PaquetDAO/DisplayPaquetDAO.php';
require_once __DIR__ . '/DAO/PaquetDAO/EditPaquet/CreatePaquetDAO.php';
require_once __DIR__ . '/DAO/PaquetDAO/EditPaquet/DeletePaquetDAO.php';
require_once __DIR__ . '/DAO/PaquetDAO/EditPaquet/EditPaquetDAO.php';
require_once __DIR__ . '/Controller/PaquetController/DisplayPaquetController/DisplayPaquetController.php';
require_once __DIR__ . '/Controller/PaquetController/EditPaquetController/CreatePaquetController.php';
require_once __DIR__ . '/Controller/PaquetController/EditPaquetController/DeletePaquetController.php';
require_once __DIR__ . '/Controller/PaquetController/EditPaquetController/EditPaquetController.php';
require_once __DIR__ . '/DAO/HistoriqueEnvoiDAO.php';
require_once __DIR__ . '/Controller/HistoriqueEnvoiController.php';
require_once __DIR__ . '/DAO/CorpusDAO.php';
require_once __DIR__ . '/Controller/CorpusController.php';
require_once __DIR__ . '/Controller/Auth/LoginController.php';
require_once __DIR__ . '/Controller/Auth/RegisterController.php';
require_once __DIR__ . '/DAO/TypeDocumentDAO.php';
require_once __DIR__ . '/Controller/TypeDocumentController.php';


$authController = new AuthController();
$pdo = Database::getConnexion();
$userDao = new UsersDAO($pdo);
$usersController = new UsersController($userDao);
$paquetDao = new DisplayPaquetDAO($pdo);
$createPaquetDao = new CreatePaquetDAO($pdo);
$deletePaquetDao = new DeletePaquetDAO($pdo);
$editPaquetDao = new EditPaquetDAO($pdo);
$historiqueEnvoiDao = new HistoriqueEnvoiDAO($pdo);
$historiqueEnvoiController = new HistoriqueEnvoiController($historiqueEnvoiDao);
$corpusDao = new CorpusDAO($pdo);
$corpusController = new CorpusController($corpusDao);
$typeDocumentDao = new TypeDocumentDAO($pdo);
$typeDocumentController = new TypeDocumentController($typeDocumentDao);


$page = $_GET["page"] ?? "user";
$action = $_GET["action"] ?? null;

switch ($action) {
    // TypeDocument
    case 'display-type-documents':
        $typeDocumentController->displayAllTypeDocuments();
        break;
    case 'display-type-document':
        $typeDocumentController->handleGetTypeDocumentRequest();
        break;
        // Vérification du token JWT via cookie
        case 'check-auth':
            $authController->checkAuth();
            break;
    // Edition User
        case 'update-user':
            $usersController->updateUser();
            break;
        case 'update-user-password':
            $usersController->updateUserPassword();
            break;
        case 'delete-user':
            $usersController->deleteUser();
            break;
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
        // Affichage des utilisateurs
    case 'get-users':
        $usersController->getAllUsers();
        break;
    case 'get-user':
        $usersController->handleGetUserRequest();
        break;
        // Afficher Paquet
    case 'display-paquets':
        $displayPaquet = new DisplayPaquetController($paquetDao);
        $displayPaquet->displayPaquet();
        break;
    case 'display-paquet':
        $displayPaquet = new DisplayPaquetController($paquetDao);
        $displayPaquet->displayPaquetByCote();
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
        $editPaquetController = new EditPaquetController($editPaquetDao);
        $editPaquetController->editPaquet();
        break;
    // Historique d'envoi
    case 'display-historiques-envoi':
        $historiqueEnvoiController->displayAllHistorySend();
        break;
    case 'display-historique-envoi':
        $historiqueEnvoiController->handleGetHistoryRequest();
        break;
    // Corpus
    case 'create-corpus':
        $corpusController->createCorpus();
        break;
    case 'delete-corpus':
        $corpusController->deleteCorpus();
        break;
    case 'edit-corpus':
        $corpusController->editCorpus();
        break;
    case 'display-corpus-all':
        $corpusController->displayAllCorpus();
        break;
    case 'display-corpus':
        $corpusController->getCorpusById((int)($_GET['id'] ?? 0));
        break;
}