<?php

namespace fathie\Repository;

use fathie\Core\Abstract\AbstractRepository;
use fathie\Entity\AchatEntity;
use fathie\Repository\IAchatRepository;

class AchatRepository extends AbstractRepository implements IAchatRepository
{
    protected string $table = 'achats';

    public function __construct()
    {
        parent::__construct();
    }

    public function save(AchatEntity $achat): AchatEntity
    {
        $sql = "INSERT INTO {$this->table} 
                (reference, numero_compteur, code_recharge, montant, nombre_kwh, tranche, prix_kwh, date_achat, heure_achat, created_at) 
                VALUES (:reference, :numero_compteur, :code_recharge, :montant, :nombre_kwh, :tranche, :prix_kwh, :date_achat, :heure_achat, :created_at)";
        
        $stmt = $this->db->prepare($sql);
        $data = $achat->toArray();
        unset($data['id']);
        
        $stmt->execute($data);
        $achat->setId($this->db->lastInsertId());
        
        return $achat;
    }

    public function findByReference(string $reference): ?AchatEntity
    {
        $sql = "SELECT * FROM {$this->table} WHERE reference = :reference";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':reference', $reference);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new AchatEntity($data) : null;
    }

    public function findByCompteur(string $numeroCompteur, int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE numero_compteur = :numero ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':numero', $numeroCompteur);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(fn($data) => new AchatEntity($data), $results);
    }
}
