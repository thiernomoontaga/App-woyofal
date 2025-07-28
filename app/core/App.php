<?php
namespace fathie\core;

use Symfony\Component\Yaml\Yaml;
use ReflectionClass;

class fathie extends Singleton {
    private array $dependencies = [];

    protected function __construct() {
        $configPath = __DIR__ . '/../config/service.yaml';
        $config = Yaml::parseFile($configPath);

        foreach ($config as $category => $items) {
            $this->dependencies[$category] = [];

            foreach ($items as $key => $className) {
                $this->dependencies[$category][$key] = $this->resolve($className);
            }
        }
    }

    public function resolve(string $className): object {
        if (!class_exists($className)) {
            throw new \Exception("Classe '$className' introuvable.");
        }

        // Si c'est un singleton explicite
        if (method_exists($className, 'getInstance')) {
            return $className::getInstance();
        }

        $reflection = new ReflectionClass($className);

        if (!$constructor = $reflection->getConstructor()) {
            return $reflection->newInstance(); // Pas de constructeur => on instancie directement
        }

        $constructor->setAccessible(true); // Permet d'accéder à un constructeur non-public

        $params = $constructor->getParameters();
        $args = [];

        foreach ($params as $param) {
            $paramType = $param->getType();

            if (!$paramType || $paramType->isBuiltin()) {
                throw new \Exception("Impossible d'injecter la dépendance du paramètre '{$param->getName()}' dans la classe '$className'.");
            }

            $dependencyClass = $paramType->getName();
            $args[] = $this->resolve($dependencyClass);
        }

        // Rendre le constructeur accessible même s’il est protected/private
        $constructor->setAccessible(true);
        return $reflection->newInstanceArgs($args);
    }

    public static function getDependencie(string $category = null, string $key = null) {
        $fathie = static::getInstance();

        if ($category === null) {
            return $fathie->dependencies;
        }

        if (!isset($fathie->dependencies[$category])) {
            throw new \Exception("Catégorie '$category' introuvable.");
        }

        if ($key === null) {
            return $fathie->dependencies[$category];
        }

        if (!isset($fathie->dependencies[$category][$key])) {
            throw new \Exception("Clé '$key' introuvable dans '$category'.");
        }

        return $fathie->dependencies[$category][$key];
    }
}
