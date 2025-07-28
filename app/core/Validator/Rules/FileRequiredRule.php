<?php
namespace App\Core\Validator\Rules;
use App\Core\Messages\ValidationMessage;

class FileRequiredRule implements ValidationRuleInterface {
    private string $message;

    public function __construct(string|ValidationMessage $message = ValidationMessage::FILE_REQUIRED) {
        $this->message = $message instanceof ValidationMessage ?  $message->value : $message;
    }

    public function validate(string $key, $value, array &$errors): void {
        if (!isset($_FILES[$key]) || $_FILES[$key]['error'] !== 0) {
            $errors[$key][] = $this->message;
        }
    }
}