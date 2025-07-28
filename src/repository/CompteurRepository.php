<?php

namespace fathie\Repository;

use fathie\Core\Abstract\AbstractRepository;
use fathie\Entity\CompteurEntity;
use fathie\Repository\ICompteurRepository;

class CompteurRepository extends AbstractRepository implements ICompteurRepository
{
    protected string $table = 'compteurs';

    public function __construct()
    {
        parent::__construct();
    }

    public function findByNumero(string $numero): ?CompteurEntity
    {
        $sql = "SELECT c.*, cl.nom, cl.prenom 
                FROM {$this->table} c 
                LEFT JOIN clients cl ON c.client_id = cl.id 
                WHERE c.numero = :numero AND c.actif = true";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':numero', $numero);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return new CompteurEntity($data);
    }

    public function save(CompteurEntity $compteur): CompteurEntity
    {
        if ($compteur->getId()) {
            return $this->update($compteur);
        }
        
        return $this->create($compteur);
    }

    private function create(CompteurEntity $compteur): CompteurEntity
    {
        $sql = "INSERT INTO {$this->table} 
                (numero, client_id, consommation_mensuelle, date_reset_tranche, actif, created_at, updated_at) 
                VALUES (:numero, :client_id, :consommation_mensuelle, :date_reset_tranche, :actif, :created_at, :updated_at)";
        
        $stmt = $this->db->prepare($sql);
        $data = $compteur->toArray();
        unset($data['id']);
        
        $stmt->execute($data);
        $compteur->setId($this->db->lastInsertId());
        
        return $compteur;
    }

    private function update(CompteurEntity $compteur): CompteurEntity
    {
        $sql = "UPDATE {$this->table} SET 
                numero = :numero, 
                client_id = :client_id, 
                consommation_mensuelle = :consommation_mensuelle, 
                date_reset_tranche = :date_reset_tranche, 
                actif = :actif, 
                updated_at = :updated_at 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($compteur->toArray());
        
        return $compteur;
    }

    public function updateConsommation(string $numero, float $consommation): bool
    {
        $sql = "UPDATE {$this->table} SET 
                consommation_mensuelle = consommation_mensuelle + :consommation, 
                updated_at = :updated_at 
                WHERE numero = :numero";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'consommation' => $consommation,
            'numero' => $numero,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function resetTranchesMensuelles(): bool
    {
        $sql = "UPDATE {$this->table} SET 
                consommation_mensuelle = 0, 
                date_reset_tranche = :date_reset, 
                updated_at = :updated_at";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'date_reset' => date('Y-m-01'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
