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
                // Read connection settings from environment
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $port = $_ENV['DB_PORT'] ?? 3306;
                $dbname = $_ENV['DB_NAME'] ?? 'NABU';
                $user = $_ENV['DB_USER'] ?? 'root';
                $pass = $_ENV['DB_PASS'] ?? '';
                $socket = $_ENV['DB_SOCKET'] ?? null; // Optional: use a UNIX socket if provided

                // Build DSN: prefer UNIX socket when defined, otherwise host/port
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
