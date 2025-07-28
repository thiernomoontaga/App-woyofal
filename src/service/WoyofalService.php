<?php

namespace fathie\Service;

use fathie\Service\IWoyofalService;
use fathie\Repository\ICompteurRepository;
use fathie\Repository\IAchatRepository;
use fathie\Repository\IClientRepository;
use fathie\Repository\ILoggerRepository;
use fathie\Entity\AchatEntity;
use fathie\Entity\JournalEntity;

class WoyofalService implements IWoyofalService
{
    private const TRANCHES = [
        1 => ['min' => 0, 'max' => 150, 'prix' => 91],
        2 => ['min' => 151, 'max' => 250, 'prix' => 102],
        3 => ['min' => 251, 'max' => 400, 'prix' => 116],
        4 => ['min' => 401, 'max' => PHP_FLOAT_MAX, 'prix' => 132]
    ];

    public function __construct(
        private ICompteurRepository $compteurRepository,
        private IAchatRepository $achatRepository,
        private IClientRepository $clientRepository,
        private ILoggerRepository $loggerRepository
    ) {}

    public function acheterCredit(string $numeroCompteur, float $montant): array
    {
        try {
            // Vérifier l'existence du compteur
            $compteur = $this->compteurRepository->findByNumero($numeroCompteur);
            if (!$compteur) {
                $this->logTransaction($numeroCompteur, null, 'Échec', 'Compteur non trouvé');
                return [
                    'data' => null,
                    'statut' => 'error',
                    'code' => 404,
                    'message' => 'Le numéro de compteur non retrouvé'
                ];
            }

            // Vérifier si les tranches doivent être réinitialisées
            $this->verifierResetTranches($compteur);

            // Calculer les tranches et KWh
            $calculTranches = $this->calculerTranches($compteur->getConsommationMensuelle(), $montant);
            
            if ($calculTranches['kwh_total'] <= 0) {
                $this->logTransaction($numeroCompteur, null, 'Échec', 'Montant insuffisant');
                return [
                    'data' => null,
                    'statut' => 'error',
                    'code' => 400,
                    'message' => 'Montant insuffisant pour générer des KWh'
                ];
            }

            // Récupérer les informations du client
            $client = $this->clientRepository->findById($compteur->getClientId());

            // Créer l'achat
            $achat = new AchatEntity();
            $achat->setNumeroCompteur($numeroCompteur)
                  ->setMontant($montant)
                  ->setNombreKwh($calculTranches['kwh_total'])
                  ->setTranche($calculTranches['tranche_principale'])
                  ->setPrixKwh($calculTranches['prix_moyen']);

            // Sauvegarder l'achat
            $achat = $this->achatRepository->save($achat);

            // Mettre à jour la consommation du compteur
            $this->compteurRepository->updateConsommation($numeroCompteur, $calculTranches['kwh_total']);

            // Logger la transaction réussie
            $this->logTransaction($numeroCompteur, $achat->getCodeRecharge(), 'Success', 'Achat effectué avec succès');

            return [
                'data' => [
                    'compteur' => $numeroCompteur,
                    'reference' => $achat->getReference(),
                    'code' => $achat->getCodeRecharge(),
                    'date' => $achat->getDateAchat() . ' ' . $achat->getHeureAchat(),
                    'tranche' => $calculTranches['tranche_principale'],
                    'prix' => $calculTranches['prix_moyen'],
                    'nbreKwt' => $calculTranches['kwh_total'],
                    'client' => $client ? $client->getNomComplet() : 'Client inconnu'
                ],
                'statut' => 'success',
                'code' => 200,
                'message' => 'Achat effectué avec succès'
            ];

        } catch (\Exception $e) {
            $this->logTransaction($numeroCompteur, null, 'Échec', $e->getMessage());
            return [
                'data' => null,
                'statut' => 'error',
                'code' => 500,
                'message' => 'Erreur interne du serveur'
            ];
        }
    }

    public function verifierCompteur(string $numeroCompteur): bool
    {
        $compteur = $this->compteurRepository->findByNumero($numeroCompteur);
        return $compteur !== null && $compteur->isActif();
    }

    public function calculerTranches(float $consommationActuelle, float $montant): array
    {
        $kwhTotal = 0;
        $montantRestant = $montant;
        $tranchePrincipale = 1;
        $prixTotal = 0;

        foreach (self::TRANCHES as $numTranche => $tranche) {
            if ($montantRestant <= 0) break;

            $consommationTranche = max(0, min($consommationActuelle, $tranche['max']) - max($consommationActuelle, $tranche['min'] - 1));
            $capaciteRestante = $tranche['max'] - max($consommationActuelle, $tranche['min'] - 1);

            if ($capaciteRestante > 0) {
                $kwhPossible = $montantRestant / $tranche['prix'];
                $kwhTranche = min($kwhPossible, $capaciteRestante);
                
                if ($kwhTranche > 0) {
                    $kwhTotal += $kwhTranche;
                    $coutTranche = $kwhTranche * $tranche['prix'];
                    $montantRestant -= $coutTranche;
                    $prixTotal += $coutTranche;
                    $tranchePrincipale = $numTranche;
                    $consommationActuelle += $kwhTranche;
                }
            }
        }

        return [
            'kwh_total' => round($kwhTotal, 2),
            'tranche_principale' => $tranchePrincipale,
            'prix_moyen' => $kwhTotal > 0 ? round($prixTotal / $kwhTotal, 2) : 0
        ];
    }

    private function verifierResetTranches($compteur): void
    {
        $dateReset = new \DateTime($compteur->getDateResetTranche());
        $maintenant = new \DateTime();
        
        if ($dateReset->format('Y-m') < $maintenant->format('Y-m')) {
            $this->compteurRepository->resetTranchesMensuelles();
        }
    }

    private function logTransaction(string $numeroCompteur, ?string $codeRecharge, string $statut, string $message): void
    {
        $journal = new JournalEntity();
        $journal->setDate(date('Y-m-d'))
                ->setHeure(date('H:i:s'))
                ->setLocalisation($_SERVER['REMOTE_ADDR'] ?? 'Unknown')
                ->setIp($_SERVER['REMOTE_ADDR'] ?? 'Unknown')
                ->setStatut($statut)
                ->setNumeroCompteur($numeroCompteur)
                ->setCodeRecharge($codeRecharge)
                ->setMessage($message);

        $this->loggerRepository->save($journal);
    }
}
