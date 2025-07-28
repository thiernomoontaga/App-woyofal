<?php
namespace App\Core\Messages;

enum ValidationMessage: string
{
    // 🧩 Règles
    case REQUIRED            = 'Ce champ est obligatoire.';
    case INVALID_PHONE       = 'Numéro de téléphone invalide.';
    case NCI_INVALID         = 'NCI invalide.';
    case NCI_EXISTS          = 'Ce NCI est déjà utilisé.';
    case PHONE_EXISTS        = 'Ce numéro est déjà utilisé.';
    case FILE_REQUIRED       = 'Ce fichier est obligatoire.';
    case FILE_UPLOAD_FAILED  = 'Erreur lors de l\'upload du fichier.';
    case FILE_NOT_FOUND      = 'Fichier introuvable.';
    case PASSWORD_MISMATCH   = 'Les mots de passe ne correspondent pas.';
    case IS_USED             = 'Cette valeur est déjà utilisée.';
    case COMPT_CREER         = 'votre compte MAXITSA a bien été créé !';
    case INVALID_TARIF       = 'Le tarif doit être un nombre positif avec au plus deux décimales.';

    // 🏷️ Champs
    case KEY_NOM                   = 'nom';
    case KEY_PRENOM                = 'prenom';
    case KEY_PASSWORD              = 'password';
    case KEY_ADRESSE               = 'adresse';
    case KEY_PHOTO_RECTO           = 'photo_recto';
    case KEY_PHOTO_VERSO           = 'photo_verso';
    case KEY_PASSWORD_CONFIRMATION = 'password_confirmation';
    case KEY_TELEPHONE             = 'telephone';
    case KEY_NCI                   = 'nci';
    case BONJOUR                   = 'Bonjour';
    case INDICATEUR                = '+221';
    case KEY_TARIF                 = 'tarif';
}