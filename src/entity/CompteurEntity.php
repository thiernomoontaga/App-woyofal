<?php

namespace fathie\Entity;

use fathie\Core\Abstract\AbstractEntity;

class CompteurEntity extends AbstractEntity
{
    private ?int $id = null;
    private string $numero;
    private int $clientId;
    private float $consommationMensuelle = 0.0;
    private string $dateResetTranche;
    private bool $actif = true;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->dateResetTranche = date('Y-m-01'); // Premier du mois
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNumero(): string { return $this->numero; }
    public function getClientId(): int { return $this->clientId; }
    public function getConsommationMensuelle(): float { return $this->consommationMensuelle; }
    public function getDateResetTranche(): string { return $this->dateResetTranche; }
    public function isActif(): bool { return $this->actif; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }

    // Setters
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setNumero(string $numero): self { $this->numero = $numero; return $this; }
    public function setClientId(int $clientId): self { $this->clientId = $clientId; return $this; }
    public function setConsommationMensuelle(float $consommation): self { 
        $this->consommationMensuelle = $consommation; 
        $this->updatedAt = date('Y-m-d H:i:s');
        return $this; 
    }
    public function setDateResetTranche(string $date): self { $this->dateResetTranche = $date; return $this; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    public static function toObject($array): static
    {
        $compteur = new static();
        $compteur->setId($array['id'] ?? null);
        $compteur->setNumero($array['numero'] ?? '');
        $compteur->setClientId($array['client_id'] ?? 0);
        $compteur->setConsommationMensuelle($array['consommation_mensuelle'] ?? 0.0);
        $compteur->setDateResetTranche($array['date_reset_tranche'] ?? date('Y-m-01'));
        $compteur->setActif($array['actif'] ?? true);
        return $compteur;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'client_id' => $this->clientId,
            'consommation_mensuelle' => $this->consommationMensuelle,
            'date_reset_tranche' => $this->dateResetTranche,
            'actif' => $this->actif,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
