<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Chemin vers le .env
$dotenv->load();

// Connexion base de données
define('DB_DRIVER', $_ENV['DB_DRIVER'] ?? 'pgsql'); // valeur par défaut = pgsql
define('HOST', $_ENV['HOST']);
define('PORT', $_ENV['PORT'] ?? '5433'); // valeur par défaut = 5432
define('DB_NAME', $_ENV['DB_NAME']);
define('USER_NAME', $_ENV['USER_NAME']);
define('PASSWORD', $_ENV['PASSWORD']);


// Twilio
define('TWILIO_SID', $_ENV['TWILIO_SID']);
define('TWILIO_TOKEN', $_ENV['TWILIO_TOKEN']);
define('TWILIO_FROM', $_ENV['TWILIO_FROM']);

// Autres configs si besoin
define('URI_HOST', $_ENV['URI_HOST']);
