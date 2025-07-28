<?php

namespace fathie\Controller;

use fathie\Core\Abstract\AbstractController;
use fathie\Service\IWoyofalService;
use fathie\Core\Session;
use fathie\Core\Validator\Validator;

class WoyofalController extends AbstractController
{
    public function __construct(
        private IWoyofalService $woyofalService,
        Session $session = null,
        Validator $validator = null
    ) {
        // Création d'instances par défaut si non fournies
        $session = $session ?? new Session();
        $validator = $validator ?? new Validator();
        parent::__construct($session, $validator);
    }

    public function acheter(): void
    {
        if ($this->getMethod() !== 'POST') {
            $this->jsonResponse([
                'data' => null,
                'statut' => 'error',
                'code' => 405,
                'message' => 'Méthode non autorisée'
            ], 405);
            return;
        }

        $input = $this->getJsonInput();
        
        // Validation des données d'entrée
        if (!isset($input['numero_compteur']) || !isset($input['montant'])) {
            $this->jsonResponse([
                'data' => null,
                'statut' => 'error',
                'code' => 400,
                'message' => 'Numéro de compteur et montant requis'
            ], 400);
            return;
        }

        $numeroCompteur = trim($input['numero_compteur']);
        $montant = floatval($input['montant']);

        // Validation du montant
        if ($montant <= 0) {
            $this->jsonResponse([
                'data' => null,
                'statut' => 'error',
                'code' => 400,
                'message' => 'Le montant doit être supérieur à zéro'
            ], 400);
            return;
        }

        // Validation du numéro de compteur
        if (empty($numeroCompteur)) {
            $this->jsonResponse([
                'data' => null,
                'statut' => 'error',
                'code' => 400,
                'message' => 'Numéro de compteur invalide'
            ], 400);
            return;
        }

        // Traitement de l'achat
        $result = $this->woyofalService->acheterCredit($numeroCompteur, $montant);
        $this->jsonResponse($result, $result['code']);
    }

    public function verifierCompteur(): void
    {
        if ($this->getMethod() !== 'GET') {
            $this->jsonResponse([
                'data' => null,
                'statut' => 'error',
                'code' => 405,
                'message' => 'Méthode non autorisée'
            ], 405);
            return;
        }

        $numeroCompteur = $_GET['numero'] ?? '';
        
        if (empty($numeroCompteur)) {
            $this->jsonResponse([
                'data' => null,
                'statut' => 'error',
                'code' => 400,
                'message' => 'Numéro de compteur requis'
            ], 400);
            return;
        }

        $existe = $this->woyofalService->verifierCompteur($numeroCompteur);
        
        $this->jsonResponse([
            'data' => ['existe' => $existe],
            'statut' => 'success',
            'code' => 200,
            'message' => $existe ? 'Compteur trouvé' : 'Compteur non trouvé'
        ]);
    }
}
