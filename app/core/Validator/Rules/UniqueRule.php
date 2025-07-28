<?php
namespace App\Core\Validator\Rules;
use App\Core\Messages\ValidationMessage;

use App\Core\Validator\Contracts\UniqueValueCheckerInterface;

class UniqueRule implements ValidationRuleInterface {
    private string $champ;
    private string $message;
    private UniqueValueCheckerInterface $checker;

    public function __construct(
        string $champ,
        UniqueValueCheckerInterface $checker,
        string|ValidationMessage $message = ValidationMessage::IS_USED
    ) {
        $this->champ = $champ;
        $this->message = $message instanceof ValidationMessage ? $message->value : $message;
        $this->checker = $checker;
    }

    public function validate(string $key, $value, array &$errors): void {
        if (!$this->checker->isUnique($this->champ, $value)) {
            $errors[$key][] = $this->message;
        }
    }
}
