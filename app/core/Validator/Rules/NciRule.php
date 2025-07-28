<?php
namespace App\Core\Validator\Rules;
use App\Core\Messages\ValidationMessage;

class NciRule implements ValidationRuleInterface {
    private string $message;

    public function __construct(string|ValidationMessage $message = ValidationMessage::NCI_INVALID) {
        $this->message = $message instanceof ValidationMessage ? $message->value : $message;
    }

    public function validate(string $key, $value, array &$errors): void {
        if (!preg_match('/^[1-2][0-9]{12}$/', $value)) {
            $errors[$key][] = $this->message;
        }
    }
}


