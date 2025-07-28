<?php
namespace App\Core\Validator\Rules;
use App\Core\Messages\ValidationMessage;

use Src\service\SecuriteService;

use App\core\App;

class UserRules
{
    private static function baseRules(): array {
        $checker = App::getDependencie('service', 'SecuriteService');

        return [
            ValidationMessage::KEY_NOM->value                       => [new RequiredRule()],
            ValidationMessage::KEY_PRENOM->value                    => [new RequiredRule()],
            ValidationMessage::KEY_PASSWORD->value                  => [new RequiredRule()],
            ValidationMessage::KEY_ADRESSE->value                   => [new RequiredRule()],
            ValidationMessage::KEY_PHOTO_RECTO->value               => [new FileRequiredRule()],
            ValidationMessage::KEY_PHOTO_VERSO->value               => [new FileRequiredRule()],
            ValidationMessage::KEY_TARIF->value                     => [new RequiredRule(), new TarifRule()],
            ValidationMessage::KEY_PASSWORD_CONFIRMATION->value     => [new Compare(ValidationMessage::KEY_PASSWORD->value)],
            ValidationMessage::KEY_TELEPHONE->value                 => [
                                                                        new RequiredRule(),
                                                                        new SenegalPhoneRule(),
                                                                        new UniqueRule('num_tel', $checker, ValidationMessage::PHONE_EXISTS->value)
                                                                    ],
            ValidationMessage::KEY_NCI->value                       => [
                                                                        new RequiredRule(),
                                                                        new NciRule(),
                                                                        new UniqueRule('nci', $checker, ValidationMessage::NCI_EXISTS->value)
                                                                    ],

        ];
    }

    public static function getRules(): array {
        return self::baseRules();
    }
    /**
     * Retourne les règles uniquement pour les champs demandés,
     * en excluant certaines règles si besoin.
     *
     * @param array $fields Champs à valider (ex. ['telephone', 'password'])
     * @param array $excludeTypes Types de règles à exclure (ex. [UniqueRule::class])
     * @return array
     */
    public static function getRulesFor(array $fields, array $excludeTypes = []): array {
        $all = self::baseRules();
        $filtered = [];

        foreach ($fields as $field) {
            if (isset($all[$field])) {
                $filtered[$field] = array_filter($all[$field], function ($rule) use ($excludeTypes) {
                    foreach ($excludeTypes as $excluded) {
                        if ($rule instanceof $excluded) {
                            return false;
                        }
                    }
                    return true;
                });
            }
        }

        return $filtered;
    }
}