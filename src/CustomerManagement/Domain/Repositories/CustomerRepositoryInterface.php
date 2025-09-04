<?php

namespace Src\CustomerManagement\Domain\Repositories;

use Src\CustomerManagement\Domain\Entities\Customer;

interface CustomerRepositoryInterface
{
    public function save(Customer $customer): Customer;

    public function findById(int $id): ?Customer;

    public function findByRfc(string $rfc): ?Customer;

    public function findByEmail(string $email): ?Customer;

    public function findAll(): array;

    public function searchByName(string $name): array;

    public function update(Customer $customer): void;

    public function delete(int $id): bool;

    public function existsWithRfc(string $rfc, ?int $excludeId = null): bool;

    public function existsWithEmail(string $email, ?int $excludeId = null): bool;
}