<?php
namespace App\Core\Validator\Contracts;

interface UniqueValueCheckerInterface {
    public function isUnique(string $field, $value): bool;
}
