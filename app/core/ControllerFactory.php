<?php
// fathie/core/ControllerFactory.php
namespace fathie\Core;

use ReflectionClass;

class ControllerFactory
{
    public static function create(string $controllerClass): object
    {
        if (!class_exists($controllerClass)) {
            throw new \Exception("Contrôleur '$controllerClass' introuvable.");
        }

        $reflection = new ReflectionClass($controllerClass);

        if (!$constructor = $reflection->getConstructor()) {
            return $reflection->newInstance();
        }

        $params = $constructor->getParameters();
        $args = [];

        foreach ($params as $param) {
            $paramType = $param->getType();
            if (!$paramType || $paramType->isBuiltin()) {
                throw new \Exception("Impossible d'injecter '{$param->getName()}' dans $controllerClass");
            }

            $dependencyClass = $paramType->getName();
            $args[] = fathie::getInstance()->resolve($dependencyClass); // Injection automatique
        }

        return $reflection->newInstanceArgs($args);
    }
}
