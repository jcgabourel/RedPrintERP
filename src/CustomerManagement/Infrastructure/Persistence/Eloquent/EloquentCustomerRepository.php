<?php

namespace Src\CustomerManagement\Infrastructure\Persistence\Eloquent;

use Src\CustomerManagement\Domain\Entities\Customer;
use Src\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;
use Src\CustomerManagement\Domain\ValueObjects\RFC;
use Src\CustomerManagement\Domain\ValueObjects\Email;
use Src\CustomerManagement\Domain\ValueObjects\PhoneNumber;
use Src\CustomerManagement\Domain\ValueObjects\Address;
use App\Models\Customer as CustomerModel;
use Illuminate\Support\Collection;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function save(Customer $customer): Customer
    {
        $customerModel = CustomerModel::updateOrCreate(
            ['id' => $customer->getId()],
            [
                'name' => $customer->getName(),
                'rfc' => $customer->getRfc()->getValue(),
                'address' => $customer->getAddress()->getValue(),
                'phone' => $customer->getPhone()->getValue(),
                'email' => $customer->getEmail()->getValue(),
                'created_at' => $customer->getCreatedAt(),
                'updated_at' => $customer->getUpdatedAt(),
            ]
        );

        return $this->mapToDomain($customerModel);
    }

    public function findById(int $id): ?Customer
    {
        $customerModel = CustomerModel::find($id);

        if (!$customerModel) {
            return null;
        }

        return $this->mapToDomain($customerModel);
    }

    public function findByRfc(string $rfc): ?Customer
    {
        $customerModel = CustomerModel::where('rfc', $rfc)->first();

        if (!$customerModel) {
            return null;
        }

        return $this->mapToDomain($customerModel);
    }

    public function findByEmail(string $email): ?Customer
    {
        $customerModel = CustomerModel::where('email', $email)->first();

        if (!$customerModel) {
            return null;
        }

        return $this->mapToDomain($customerModel);
    }

    public function findAll(): array
    {
        return CustomerModel::all()
            ->map(fn($customerModel) => $this->mapToDomain($customerModel))
            ->toArray();
    }

    public function searchByName(string $name): array
    {
        return CustomerModel::where('name', 'like', "%{$name}%")
            ->get()
            ->map(fn($customerModel) => $this->mapToDomain($customerModel))
            ->toArray();
    }

    public function update(Customer $customer): void
    {
        CustomerModel::where('id', $customer->getId())
            ->update([
                'name' => $customer->getName(),
                'rfc' => $customer->getRfc()->getValue(),
                'address' => $customer->getAddress()->getValue(),
                'phone' => $customer->getPhone()->getValue(),
                'email' => $customer->getEmail()->getValue(),
                'updated_at' => $customer->getUpdatedAt(),
            ]);
    }

    public function delete(int $id): bool
    {
        return (bool) CustomerModel::destroy($id);
    }

    public function existsWithRfc(string $rfc, ?int $excludeId = null): bool
    {
        $query = CustomerModel::where('rfc', $rfc);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function existsWithEmail(string $email, ?int $excludeId = null): bool
    {
        $query = CustomerModel::where('email', $email);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    private function mapToDomain(CustomerModel $customerModel): Customer
    {
        return new Customer(
            $customerModel->id,
            $customerModel->name,
            new RFC($customerModel->rfc),
            new Address($customerModel->address),
            new PhoneNumber($customerModel->phone),
            new Email($customerModel->email),
            $customerModel->created_at,
            $customerModel->updated_at
        );
    }
}