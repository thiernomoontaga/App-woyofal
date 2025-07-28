<?php

namespace fathie\Repository;

use fathie\Entity\JournalEntity;

interface ILoggerRepository
{
    public function save(JournalEntity $journal): JournalEntity;
    public function findByDate(string $date): array;
    public function findByCompteur(string $numeroCompteur): array;
    public function findAll(): array;
}
