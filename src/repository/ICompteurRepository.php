<?php

namespace fathie\Repository;

use fathie\Entity\CompteurEntity;

interface ICompteurRepository
{
    public function findByNumero(string $numero): ?CompteurEntity;
    public function save(CompteurEntity $compteur): CompteurEntity;
    public function updateConsommation(string $numero, float $consommation): bool;
    public function resetTranchesMensuelles(): bool;
}
