<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnexion(): ?PDO
    {
        if (self::$pdo === null) {
            try {
                
                $host = $_ENV['DB_HOST'];
                $port = $_ENV['DB_PORT'];
                $dbname = $_ENV['DB_NAME'];
                $user = $_ENV['DB_USER'];
                $pass = $_ENV['DB_PASS'];
                $socket = $_ENV['DB_SOCKET']; 
                $dsn = $socket
                    ? "mysql:unix_socket={$socket};dbname={$dbname};charset=utf8mb4"
                    : "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

                self::$pdo = new PDO($dsn, $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'Erreur de connexion avec la base de donnée : ' . $e->getMessage();
                return null;
            }
        }
        return self::$pdo;
    }
}
