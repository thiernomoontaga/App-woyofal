<?php
namespace fathie\Core;

class Session {
    private static ?Session $instance = null;

    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Singleton : retourne l'unique instance
    public static function getInstance(): Session {
        if (self::$instance === null) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    // Ajouter une donnée à la session
    public function set(string $key, $data): void {
        $_SESSION[$key] = $data;
    }

    // Récupérer une donnée de la session
    public function get(string $key) {
        return $_SESSION[$key] ?? null;
    }

    // Supprimer une donnée de la session
    public function unset(string $key): void {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    // Vérifie si une clé existe dans la session
    public function isset(string $key): bool {
        return isset($_SESSION[$key]);
    }

    // Détruire entièrement la session
    public function destroy(): void {
        session_unset();  // Supprime toutes les variables de session
        session_destroy(); // Détruit la session
        self::$instance = null; // Réinitialise l'instance
    }
}
