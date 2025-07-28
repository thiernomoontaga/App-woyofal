<?php
namespace App\Core\Validator\Rules;
use App\Core\Messages\ValidationMessage;

class SenegalPhoneRule implements ValidationRuleInterface {
    private string $message;

    public function __construct(string|ValidationMessage $message = ValidationMessage::INVALID_PHONE ) {
        $this->message = $message instanceof ValidationMessage ? $message->value : $message;

    }

    public function validate(string $key, $value, array &$errors): void {
        if (!preg_match('/^(77|78|70|76|75)[0-9]{7}$/', $value)) {
            $errors[$key][] = $this->message;
        }
    }
}
