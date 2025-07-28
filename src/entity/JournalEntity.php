<?php

namespace fathie\Entity;

use fathie\Core\Abstract\AbstractEntity;

class JournalEntity extends AbstractEntity
{
    private ?int $id = null;
    private string $date;
    private string $heure;
    private string $localisation;
    private string $ip;
    private string $statut;
    private string $numeroCompteur;
    private ?string $codeRecharge = null;
    private string $message;
    private string $createdAt;

    public function __construct(array $data = [])
    {
        $this->date = date('Y-m-d');
        $this->heure = date('H:i:s');
        $this->createdAt = date('Y-m-d H:i:s');
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getDate(): string { return $this->date; }
    public function getHeure(): string { return $this->heure; }
    public function getLocalisation(): string { return $this->localisation; }
    public function getIp(): string { return $this->ip; }
    public function getStatut(): string { return $this->statut; }
    public function getNumeroCompteur(): string { return $this->numeroCompteur; }
    public function getCodeRecharge(): ?string { return $this->codeRecharge; }
    public function getMessage(): string { return $this->message; }
    public function getCreatedAt(): string { return $this->createdAt; }

    // Setters
    public function setId(?int $id): self { $this->id = $id; return $this; }
    public function setDate(string $date): self { $this->date = $date; return $this; }
    public function setHeure(string $heure): self { $this->heure = $heure; return $this; }
    public function setLocalisation(string $localisation): self { $this->localisation = $localisation; return $this; }
    public function setIp(string $ip): self { $this->ip = $ip; return $this; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function setNumeroCompteur(string $numero): self { $this->numeroCompteur = $numero; return $this; }
    public function setCodeRecharge(?string $code): self { $this->codeRecharge = $code; return $this; }
    public function setMessage(string $message): self { $this->message = $message; return $this; }

    public static function toObject($array): static
    {
        $journal = new static();
        $journal->setId($array['id'] ?? null);
        $journal->setDate($array['date'] ?? date('Y-m-d'));
        $journal->setHeure($array['heure'] ?? date('H:i:s'));
        $journal->setLocalisation($array['localisation'] ?? '');
        $journal->setIp($array['ip'] ?? '');
        $journal->setStatut($array['statut'] ?? '');
        $journal->setNumeroCompteur($array['numero_compteur'] ?? '');
        $journal->setCodeRecharge($array['code_recharge'] ?? null);
        $journal->setMessage($array['message'] ?? '');
        return $journal;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'heure' => $this->heure,
            'localisation' => $this->localisation,
            'ip' => $this->ip,
            'statut' => $this->statut,
            'numero_compteur' => $this->numeroCompteur,
            'code_recharge' => $this->codeRecharge,
            'message' => $this->message,
            'created_at' => $this->createdAt
        ];
    }
}
