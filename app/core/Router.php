<?php

namespace fathie\Core;

use Src\controller\ErrorController;
use fathie\Core\ControllerFactory;
use fathie\Core\Page404;

class Router
{
    public static function resolve(array $routes): void
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!isset($routes[$url])) {
            self::handleNotFound();
            return;
        }

        $route = $routes[$url];
        $controllerClass = $route['controller'] ?? null;
        $action = $route['action'] ?? null;

        if (!$controllerClass || !$action) {
            self::handleNotFound();
            return;
        }

        // Gestion des middlewares
        $middlewaresConfig = require __DIR__ . '/../config/middlewares.php';
        $middlewares = $route['middleware'] ?? [];

        foreach ($middlewares as $middlewareKey) {
            if (!isset($middlewaresConfig[$middlewareKey])) {
                throw new \Exception("Middleware '$middlewareKey' introuvable.");
            }

            $middleware = new $middlewaresConfig[$middlewareKey];
            if (!is_callable($middleware)) {
                throw new \Exception("Le middleware '$middlewareKey' n'est pas invocable.");
            }

            $middleware(); // Exécution via __invoke()
        }

        try {
            // Utilisation de ControllerFactory pour l'injection automatique
            $controller = ControllerFactory::create($controllerClass);
            if (!method_exists($controller, $action)) {
                throw new \Exception("Action '$action' introuvable dans le contrôleur '$controllerClass'.");
            }

            $controller->$action();
        } catch (\Throwable $e) {
            // Gestion des erreurs du contrôleur
            error_log($e->getMessage());
            self::handleServerError($e);
        }
    }

    private static function handleNotFound(): void
    {
        $controller = new ErrorController();
        $controller->page404();
    }

    private static function handleServerError(\Throwable $e): void
    {
        // Tu peux personnaliser cette méthode pour afficher une vraie page d'erreur 500
        http_response_code(500);
        echo "<h1>Erreur serveur</h1>";
        echo "<p>{$e->getMessage()}</p>";
    }
}
