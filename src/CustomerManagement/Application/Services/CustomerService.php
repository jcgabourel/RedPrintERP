<?php

namespace Src\CustomerManagement\Application\Services;

use Src\CustomerManagement\Domain\Entities\Customer;
use Src\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;
use Src\CustomerManagement\Domain\ValueObjects\RFC;
use Src\CustomerManagement\Domain\ValueObjects\Email;
use Src\CustomerManagement\Domain\ValueObjects\PhoneNumber;
use Src\CustomerManagement\Domain\ValueObjects\Address;
use InvalidArgumentException;

class CustomerService
{
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function createCustomer(
        string $name,
        string $rfc,
        string $address,
        string $phone,
        string $email
    ): Customer {
        // Validate business rules before creation
        $this->validateCustomerData($name, $rfc, $email, null);

        $rfcVo = new RFC($rfc);
        $emailVo = new Email($email);
        $phoneVo = new PhoneNumber($phone);
        $addressVo = new Address($address);

        $customer = Customer::create($name, $rfcVo, $addressVo, $phoneVo, $emailVo);

        return $this->customerRepository->save($customer);
    }

    public function updateCustomer(
        int $id,
        string $name,
        string $rfc,
        string $address,
        string $phone,
        string $email
    ): Customer {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            throw new InvalidArgumentException("Customer with ID {$id} not found");
        }

        // Validate business rules before update
        $this->validateCustomerData($name, $rfc, $email, $id);

        $rfcVo = new RFC($rfc);
        $emailVo = new Email($email);
        $phoneVo = new PhoneNumber($phone);
        $addressVo = new Address($address);

        $customer->update($name, $rfcVo, $addressVo, $phoneVo, $emailVo);

        $this->customerRepository->update($customer);

        return $customer;
    }

    public function getCustomerById(int $id): ?Customer
    {
        return $this->customerRepository->findById($id);
    }

    public function getCustomerByRfc(string $rfc): ?Customer
    {
        return $this->customerRepository->findByRfc($rfc);
    }

    public function getCustomerByEmail(string $email): ?Customer
    {
        return $this->customerRepository->findByEmail($email);
    }

    public function getAllCustomers(): array
    {
        return $this->customerRepository->findAll();
    }

    public function searchCustomersByName(string $name): array
    {
        return $this->customerRepository->searchByName($name);
    }

    public function deleteCustomer(int $id): bool
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            throw new InvalidArgumentException("Customer with ID {$id} not found");
        }

        return $this->customerRepository->delete($id);
    }

    private function validateCustomerData(
        string $name,
        string $rfc,
        string $email,
        ?int $excludeId = null
    ): void {
        if (empty($name)) {
            throw new InvalidArgumentException('Customer name is required');
        }

        if (strlen($name) < 2) {
            throw new InvalidArgumentException('Customer name is too short');
        }

        if (strlen($name) > 255) {
            throw new InvalidArgumentException('Customer name is too long');
        }

        // Check if RFC already exists
        if ($this->customerRepository->existsWithRfc($rfc, $excludeId)) {
            throw new InvalidArgumentException('A customer with this RFC already exists');
        }

        // Check if email already exists
        if ($this->customerRepository->existsWithEmail($email, $excludeId)) {
            throw new InvalidArgumentException('A customer with this email already exists');
        }
    }

    public function customerExists(int $id): bool
    {
        return $this->customerRepository->findById($id) !== null;
    }

    public function getCustomerStatistics(): array
    {
        $customers = $this->customerRepository->findAll();
        
        return [
            'total_customers' => count($customers),
            'recent_customers' => array_filter($customers, function (Customer $customer) {
                return $customer->getCreatedAt() > new \DateTime('-30 days');
            }),
        ];
    }
}