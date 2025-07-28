<?php
// namespace App\Core\Messages;

// class MessageBag {
//     public static function get(string $key): string {
//         $messages = [
//             'required'             => 'Ce champ est obligatoire.',
//             'invalid_phone'        => 'Numéro de téléphone invalide.',
//             'nci_invalid'          => 'NCI invalide.',
//             'nci_exists'           => 'Ce NCI est déjà utilisé.',
//             'phone_exists'         => 'Ce numéro est déjà utilisé.',
//             'file_required'        => 'Ce fichier est obligatoire.',
//             'password_mismatch'    => 'Les mots de passe ne correspondent pas.',

//             // Ajout dynamique
//             'fields' => [
//                 'nom' => 'Nom',
//                 'prenom' => 'Prénom',
//                 'telephone' => 'Téléphone',
//                 'password' => 'Mot de passe',
//                 'password_confirmation' => 'Confirmation du mot de passe',
//                 'adresse' => 'Adresse',
//                 'nci' => 'NCI',
//                 'photo_recto' => 'Photo recto',
//                 'photo_verso' => 'Photo verso'
//             ]
//         ];

//         return $messages[$key] ?? 'Message introuvable.';
//     }

//     public static function field(string $key): string {
//         $fields = self::get('fields');
//         return $fields[$key] ?? $key;
//     }
// }
