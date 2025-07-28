<?php
namespace App\Core;

use App\Core\Messages\ValidationMessage;
use App\Core\Validator\Validator;

class FileUpload
{
    private string $uploadDir;

    public function __construct(string $uploadDir = __DIR__ . '/../../public/uploads/')
    {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $this->uploadDir = $uploadDir;
    }

    public function upload(array $file, string $prefix = '', ?string $fieldKey = null): ?string
    {
        $validator = Validator::getInstance();

        if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            if ($fieldKey) {
                $validator->addError($fieldKey, ValidationMessage::FILE_NOT_FOUND->value);
            }
            return null;
        }

        $filename = uniqid($prefix) . '_' . basename($file['name']);
        $destination = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            if ($fieldKey) {
                $validator->addError($fieldKey, ValidationMessage::FILE_UPLOAD_FAILED->value);
            }
            return null;
        }

        return $filename;
    }
}
