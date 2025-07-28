<?php

namespace fathie\Repository;

use fathie\Entity\ClientEntity;

interface IClientRepository
{
    public function findById(int $id): ?ClientEntity;
    public function findByTelephone(string $telephone): ?ClientEntity;
    public function save(ClientEntity $client): ClientEntity;
    public function update(ClientEntity $client): bool;
    public function delete(int $id): bool;
    public function findAll(): array;
}
