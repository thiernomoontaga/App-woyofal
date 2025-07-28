<?php
namespace App\Core\Validator\Rules;

use App\Core\Messages\ValidationMessage;

class TarifRule implements ValidationRuleInterface {
    private string $message;

    public function __construct(string|ValidationMessage $message = ValidationMessage::INVALID_TARIF) {
        $this->message = $message instanceof ValidationMessage ? $message->value : $message;
    }

    public function validate(string $key, $value, array &$errors): void {
        // On considère que le tarif doit être un nombre positif, avec max 2 décimales
        if (!is_numeric($value) || $value <= 0 || !preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
            $errors[$key][] = $this->message;
        }
    }
}
