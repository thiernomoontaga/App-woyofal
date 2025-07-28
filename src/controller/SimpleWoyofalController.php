<?php

namespace fathie\Controller;

use fathie\Core\Database;
use PDO;

class SimpleWoyofalController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function acheter(): void
    {
        try {
            // Vérifier que c'est une requête POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse([
                    'data' => null,
                    'statut' => 'error',
                    'code' => 405,
                    'message' => 'Méthode non autorisée'
                ], 405);
                return;
            }

            // Récupérer les données JSON
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['numero_compteur']) || !isset($input['montant'])) {
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

            // Vérifier l'existence du compteur
            $sql = "SELECT c.*, cl.nom, cl.prenom 
                    FROM compteurs c 
                    LEFT JOIN clients cl ON c.client_id = cl.id 
                    WHERE c.numero = :numero AND c.actif = true";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numero', $numeroCompteur);
            $stmt->execute();
            
            $compteur = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$compteur) {
                $this->jsonResponse([
                    'data' => null,
                    'statut' => 'error',
                    'code' => 404,
                    'message' => 'Le numéro de compteur non retrouvé'
                ], 404);
                return;
            }

            // Calculer les tranches et KWh
            $calculTranches = $this->calculerTranches($compteur['consommation_mensuelle'], $montant);
            
            if ($calculTranches['kwh_total'] <= 0) {
                $this->jsonResponse([
                    'data' => null,
                    'statut' => 'error',
                    'code' => 400,
                    'message' => 'Montant insuffisant pour générer des KWh'
                ], 400);
                return;
            }

            // Générer la référence et le code de recharge
            $reference = $this->genererReference();
            $codeRecharge = $this->genererCodeRecharge();
            $dateActuelle = date('Y-m-d H:i:s');

            // Enregistrer l'achat dans la base
            $sqlInsert = "INSERT INTO achats 
                         (reference, numero_compteur, code_recharge, montant, nombre_kwh, tranche, prix_kwh, date_achat, heure_achat, created_at) 
                         VALUES (:reference, :numero_compteur, :code_recharge, :montant, :nombre_kwh, :tranche, :prix_kwh, :date_achat, :heure_achat, :created_at)";
            
            $stmtInsert = $this->db->prepare($sqlInsert);
            $stmtInsert->execute([
                ':reference' => $reference,
                ':numero_compteur' => $numeroCompteur,
                ':code_recharge' => $codeRecharge,
                ':montant' => $montant,
                ':nombre_kwh' => $calculTranches['kwh_total'],
                ':tranche' => $calculTranches['tranche_principale'],
                ':prix_kwh' => $calculTranches['prix_moyen'],
                ':date_achat' => date('Y-m-d'),
                ':heure_achat' => date('H:i:s'),
                ':created_at' => $dateActuelle
            ]);

            // Mettre à jour la consommation du compteur
            $nouvelleConsommation = $compteur['consommation_mensuelle'] + $calculTranches['kwh_total'];
            $sqlUpdate = "UPDATE compteurs SET consommation_mensuelle = :consommation, updated_at = :updated_at WHERE numero = :numero";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':consommation' => $nouvelleConsommation,
                ':updated_at' => $dateActuelle,
                ':numero' => $numeroCompteur
            ]);

            // Journaliser la transaction
            $this->logTransaction($numeroCompteur, $codeRecharge, 'Success', 'Achat effectué avec succès');

            // Réponse de succès
            $this->jsonResponse([
                'data' => [
                    'compteur' => $numeroCompteur,
                    'reference' => $reference,
                    'code' => $codeRecharge,
                    'date' => $dateActuelle,
                    'tranche' => $calculTranches['tranche_principale'],
                    'prix' => $calculTranches['prix_moyen'],
                    'nbreKwt' => $calculTranches['kwh_total'],
                    'client' => $compteur['prenom'] . ' ' . $compteur['nom']
                ],
                'statut' => 'success',
                'code' => 200,
                'message' => 'Achat effectué avec succès'
            ], 200);

        } catch (\Exception $e) {
            error_log("Erreur achat crédit: " . $e->getMessage());
            $this->jsonResponse([
                'data' => null,
                'statut' => 'error',
                'code' => 500,
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }

    private function calculerTranches(float $consommationActuelle, float $montant): array
    {
        // Tranches tarifaires en FCFA/kWh
        $tranches = [
            1 => ['min' => 0, 'max' => 150, 'prix' => 91],
            2 => ['min' => 151, 'max' => 250, 'prix' => 102],
            3 => ['min' => 251, 'max' => 400, 'prix' => 116],
            4 => ['min' => 401, 'max' => PHP_FLOAT_MAX, 'prix' => 132]
        ];

        $kwhTotal = 0;
        $montantRestant = $montant;
        $tranchePrincipale = 1;
        $prixTotal = 0;

        foreach ($tranches as $numTranche => $tranche) {
            if ($montantRestant <= 0) break;

            // Calculer la capacité restante dans cette tranche
            $minTranche = max($consommationActuelle, $tranche['min']);
            $maxTranche = $tranche['max'];
            
            if ($minTranche >= $maxTranche) continue;

            $capaciteRestante = $maxTranche - $minTranche;

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

    private function genererReference(): string
    {
        return 'WYF' . date('YmdHis') . rand(1000, 9999);
    }

    private function genererCodeRecharge(): string
    {
        return str_pad(rand(0, 999999999), 20, '0', STR_PAD_LEFT);
    }

    private function logTransaction(string $numeroCompteur, string $codeRecharge, string $statut, string $message): void
    {
        try {
            $sql = "INSERT INTO journal (date, heure, localisation, ip, statut, numero_compteur, code_recharge, message, created_at) 
                    VALUES (:date, :heure, :localisation, :ip, :statut, :numero_compteur, :code_recharge, :message, :created_at)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':date' => date('Y-m-d'),
                ':heure' => date('H:i:s'),
                ':localisation' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                ':statut' => $statut,
                ':numero_compteur' => $numeroCompteur,
                ':code_recharge' => $codeRecharge,
                ':message' => $message,
                ':created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            error_log("Erreur log transaction: " . $e->getMessage());
        }
    }

    public function verifierCompteur(): void
    {
        try {
            $numero = $_GET['numero'] ?? '';
            
            if (empty($numero)) {
                $this->jsonResponse([
                    'data' => null,
                    'statut' => 'error',
                    'code' => 400,
                    'message' => 'Numéro de compteur requis'
                ], 400);
                return;
            }

            // Vérifier si le compteur existe
            $sql = "SELECT c.*, cl.nom, cl.prenom 
                    FROM compteurs c 
                    LEFT JOIN clients cl ON c.client_id = cl.id 
                    WHERE c.numero = :numero AND c.actif = true";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numero', $numero);
            $stmt->execute();
            
            $compteur = $stmt->fetch(PDO::FETCH_ASSOC);
            $existe = $compteur !== false;

            $response = [
                'data' => [
                    'existe' => $existe,
                    'compteur' => $existe ? [
                        'numero' => $compteur['numero'],
                        'client' => $compteur['prenom'] . ' ' . $compteur['nom'],
                        'consommation_mensuelle' => (float)$compteur['consommation_mensuelle'],
                        'actif' => (bool)$compteur['actif']
                    ] : null
                ],
                'statut' => 'success',
                'code' => 200,
                'message' => $existe ? 'Compteur trouvé' : 'Compteur non trouvé'
            ];

            $this->jsonResponse($response);

        } catch (\Exception $e) {
            error_log("Erreur vérification compteur: " . $e->getMessage());
            $this->jsonResponse([
                'data' => null,
                'statut' => 'error',
                'code' => 500,
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }

    public function getCompteurs(): void
    {
        try {
            $sql = "SELECT c.*, cl.nom, cl.prenom 
                    FROM compteurs c 
                    LEFT JOIN clients cl ON c.client_id = cl.id 
                    WHERE c.actif = true 
                    ORDER BY c.numero";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $compteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'data' => array_map(function($compteur) {
                    return [
                        'numero' => $compteur['numero'],
                        'client' => $compteur['prenom'] . ' ' . $compteur['nom'],
                        'consommation_mensuelle' => (float)$compteur['consommation_mensuelle'],
                        'actif' => (bool)$compteur['actif']
                    ];
                }, $compteurs),
                'statut' => 'success',
                'code' => 200,
                'message' => 'Liste des compteurs récupérée'
            ];

            $this->jsonResponse($response);

        } catch (\Exception $e) {
            error_log("Erreur récupération compteurs: " . $e->getMessage());
            $this->jsonResponse([
                'data' => null,
                'statut' => 'error',
                'code' => 500,
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }

    private function jsonResponse(array $data, int $httpCode = 200): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}
