<?php

namespace fathie\Entity;

use fathie\Core\Abstract\AbstractEntity;

class AchatEntity extends AbstractEntity
{
    private ?int $id = null;
    private string $reference;
    private string $numeroCompteur;
    private string $codeRecharge;
    private float $montant;
    private float $nombreKwh;
    private int $tranche;
    private float $prixKwh;
    private string $dateAchat;
    private string $heureAchat;
    private string $createdAt;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->reference = $this->generateReference();
        $this->codeRecharge = $this->generateCodeRecharge();
        $this->dateAchat = date('Y-m-d');
        $this->heureAchat = date('H:i:s');
        $this->createdAt = date('Y-m-d H:i:s');
    }

    private function generateReference(): string
    {
        return 'WYF' . date('YmdHis') . rand(1000, 9999);
    }

    private function generateCodeRecharge(): string
    {
        return str_pad(rand(0, 99999999999999999999), 20, '0', STR_PAD_LEFT);
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getReference(): string { return $this->reference; }
    public function getNumeroCompteur(): string { return $this->numeroCompteur; }
    public function getCodeRecharge(): string { return $this->codeRecharge; }
    public function getMontant(): float { return $this->montant; }
    public function getNombreKwh(): float { return $this->nombreKwh; }
    public function getTranche(): int { return $this->tranche; }
    public function getPrixKwh(): float { return $this->prixKwh; }
    public function getDateAchat(): string { return $this->dateAchat; }
    public function getHeureAchat(): string { return $this->heureAchat; }
    public function getCreatedAt(): string { return $this->createdAt; }

    // Setters
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setReference(string $reference): self { $this->reference = $reference; return $this; }
    public function setNumeroCompteur(string $numero): self { $this->numeroCompteur = $numero; return $this; }
    public function setCodeRecharge(string $code): self { $this->codeRecharge = $code; return $this; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }
    public function setNombreKwh(float $kwh): self { $this->nombreKwh = $kwh; return $this; }
    public function setTranche(int $tranche): self { $this->tranche = $tranche; return $this; }
    public function setPrixKwh(float $prix): self { $this->prixKwh = $prix; return $this; }

    public static function toObject($array): static
    {
        $achat = new static();
        $achat->setId($array['id'] ?? null);
        $achat->setReference($array['reference'] ?? '');
        $achat->setNumeroCompteur($array['numero_compteur'] ?? '');
        $achat->setCodeRecharge($array['code_recharge'] ?? '');
        $achat->setMontant($array['montant'] ?? 0.0);
        $achat->setNombreKwh($array['nombre_kwh'] ?? 0.0);
        $achat->setTranche($array['tranche'] ?? 1);
        $achat->setPrixKwh($array['prix_kwh'] ?? 0.0);
        return $achat;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'numero_compteur' => $this->numeroCompteur,
            'code_recharge' => $this->codeRecharge,
            'montant' => $this->montant,
            'nombre_kwh' => $this->nombreKwh,
            'tranche' => $this->tranche,
            'prix_kwh' => $this->prixKwh,
            'date_achat' => $this->dateAchat,
            'heure_achat' => $this->heureAchat,
            'created_at' => $this->createdAt
        ];
    }
}
