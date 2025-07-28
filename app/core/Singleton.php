<?php
namespace fathie\Core;

use ReflectionClass;

abstract class Singleton
{
    private static array $instances = [];

    public static function getInstance(): static
    {
        $class = static::class;

        if (!isset(self::$instances[$class])) {
            $reflection = new ReflectionClass($class);
            // Crée une instance même si le constructeur est privé ou protégé
            self::$instances[$class] = $reflection->newInstanceWithoutConstructor();

            // fathieelle le constructeur manuellement s’il existe
            $constructor = $reflection->getConstructor();
            if ($constructor) {
                $constructor->setAccessible(true);
                $constructor->invoke(self::$instances[$class]);
            }
        }

        return self::$instances[$class];
    }

    // On protège le constructeur et on bloque le clonage
    // protected function __construct() {}
    // final private function __clone() {}
    // final private function __wakeup() {}
}















// namespace fathie\core;

// abstract class Singleton
// {
//     private static array $instances = [];

//     public static function getInstance(): static {
//         $class = static::class;

//         if (!isset(self::$instances[$class])) {
//             self::$instances[$class] = new static();
//         }

//         return self::$instances[$class];
//     }

//     // On protège le constructeur et on bloque le clonage
//     protected function __construct() {}
//     final private function __clone() {}
//     final private function __wakeup() {}
// }
