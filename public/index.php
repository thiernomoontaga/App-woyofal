<?php

// Point d'entrée principal de l'application
declare(strict_types=1);

// Chargement de l'autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Chargement de la configuration d'environnement
require_once __DIR__ . '/../app/config/env.php';

use fathie\Core\SimpleRouter;
use fathie\Controller\SimpleWoyofalController;

// Gestion des erreurs
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gestion des requêtes OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Initialisation du router
    $router = new SimpleRouter();

    // Initialisation du contrôleur simplifié
    $woyofalController = new SimpleWoyofalController();

    // Définition des routes API
    $router->addRoute('POST', '/api/acheter', [$woyofalController, 'acheter']);
    $router->addRoute('GET', '/api/verifier-compteur', [$woyofalController, 'verifierCompteur']);
    $router->addRoute('GET', '/api/compteurs', [$woyofalController, 'getCompteurs']);

    // Route de santé
    $router->addRoute('GET', '/api/health', function() {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'OK',
            'service' => 'AppWoyofal',
            'version' => '1.0.0',
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => 'production'
        ]);
    });

    // Route par défaut
    $router->addRoute('GET', '/', function() {
        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'API Woyofal - Service de crédit électrique',
            'version' => '1.0.0',
            'endpoints' => [
                'POST /api/acheter' => 'Acheter du crédit électrique',
                'GET /api/verifier-compteur' => 'Vérifier l\'existence d\'un compteur',
                'GET /api/health' => 'État de santé du service'
            ]
        ]);
    });

    // Traitement de la requête
    $router->handleRequest();

} catch (Exception $e) {
    // Gestion globale des erreurs
    error_log("Erreur application: " . $e->getMessage());
    
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'data' => null,
        'statut' => 'error',
        'code' => 500,
        'message' => 'Erreur interne du serveur',
        'details' => $e->getMessage() // En production, ne pas exposer les détails
    ]);
}
