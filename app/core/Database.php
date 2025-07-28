<?php
namespace fathie\Core;

use PDO;
use PDOException;
class Database {
    private static ?PDO $pdo = null;
    private static ?Database $instance = null;

    private function __construct() {}

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            $host = HOST;
            $dbname = DB_NAME;
            $username = USER_NAME;
            $password = PASSWORD;

            try {
                self::$pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}