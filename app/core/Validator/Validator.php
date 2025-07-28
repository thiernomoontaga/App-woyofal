<?php
namespace fathie\Core\Validator;
use fathie\Core\Validator\Rules\ValidationRuleInterface;
use fathie\Core\Singleton;

class Validator extends Singleton {
    private static ?Validator $instance = null; // instance unique
    private array $errors = [];

    // Constructeur privé pour empêcher l'instanciation externe
    public function __construct() {}

    // Méthode pour récupérer l’instance unique
    // public static function getInstance(): Validator {
    //     if (self::$instance === null) {
    //         self::$instance = new Validator();
    //     }
    //     return self::$instance;
    // }

    public function validate(array $data, array $rules): bool {
        $this->errors = []; // Réinitialiser les erreurs avant chaque validation

        foreach ($rules as $field => $validators) {
            foreach ($validators as $rule) {
                if ($rule instanceof ValidationRuleInterface) {
                    $rule->validate($field, $data[$field] ?? null, $this->errors);
                }
            }
        }
        return empty($this->errors);
    }
    
    public function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
