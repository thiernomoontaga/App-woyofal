<?php

use App\Controller\WoyofalController;

// Routes API Woyofal
$router->post('/api/woyofal/acheter', [WoyofalController::class, 'acheter']);
$router->get('/api/woyofal/verifier-compteur', [WoyofalController::class, 'verifierCompteur']);

// Route de santé
$router->get('/api/health', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'OK',
        'service' => 'AppWoyofal',
        'version' => '1.0.0',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});
