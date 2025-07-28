<?php
namespace App\Core\Validator\Rules;
use App\Core\Messages\ValidationMessage;


class Compare implements ValidationRuleInterface {
    private string $otherField;
    private string $message;

    public function __construct(string $otherField, string|ValidationMessage $message = ValidationMessage::PASSWORD_MISMATCH) {
        $this->otherField = $otherField;
        $this->message = $message instanceof ValidationMessage ?  $message->value : $message;
    }

    public function validate(string $key, $value, array &$errors): void {
        // Vérifie que le champ de comparaison existe dans le même tableau
        if (!isset($_POST[$this->otherField])) {
            $errors[$key][] = "Le champ à comparer '{$this->otherField}' est manquant.";
            return;
        }

        if ($value !== $_POST[$this->otherField]) {
            $errors[$key][] = $this->message;
        }
    }
}
