<?php

namespace fathie\Core;

class SimpleRouter
{
    private array $routes = [];

    public function addRoute(string $method, string $path, $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Si le path est index.php, récupérer le vrai path depuis QUERY_STRING
        if ($path === '/index.php' && !empty($_SERVER['QUERY_STRING'])) {
            // Séparer le path des vrais paramètres GET
            $queryParts = explode('&', $_SERVER['QUERY_STRING']);
            $path = '/' . ltrim($queryParts[0], '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                $this->executeHandler($route['handler'], $path, $route['path']);
                return;
            }
        }

        // Route non trouvée
        $this->notFound();
    }

    private function matchPath(string $routePath, string $requestPath): bool
    {
        // Simple correspondance exacte pour l'instant
        return $routePath === $requestPath;
    }

    private function executeHandler($handler, string $requestPath, string $routePath): void
    {
        try {
            if (is_callable($handler)) {
                // Fonction anonyme
                call_user_func($handler);
            } elseif (is_array($handler) && count($handler) === 2) {
                // [Controller, 'method']
                [$controller, $method] = $handler;
                if (is_object($controller)) {
                    $controller->$method();
                } else {
                    // Nom de classe
                    $instance = new $controller();
                    $instance->$method();
                }
            } else {
                throw new \Exception("Handler invalide");
            }
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'statut' => 'error',
            'code' => 404,
            'message' => 'Route non trouvée'
        ]);
    }

    private function handleError(\Throwable $e): void
    {
        error_log("Erreur router: " . $e->getMessage());
        
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'data' => null,
            'statut' => 'error',
            'code' => 500,
            'message' => 'Erreur interne du serveur',
            'details' => $e->getMessage()
        ]);
    }
}
