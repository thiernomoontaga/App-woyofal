<?php
namespace App\Core\Validator\Rules;
use App\Core\Messages\ValidationMessage;

class RequiredRule implements ValidationRuleInterface {
    private string $message;

    public function __construct(string|ValidationMessage $message = ValidationMessage::REQUIRED) {
        $this->message = $message instanceof ValidationMessage ? $message->value : $message;
    }

    public function validate(string $key, $value, array &$errors): void {
        if (empty(trim($value))) {
            $errors[$key][] = $this->message;
        }
    }
}
