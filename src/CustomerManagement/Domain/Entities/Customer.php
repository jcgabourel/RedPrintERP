<?php

namespace Src\CustomerManagement\Domain\Entities;

use Src\CustomerManagement\Domain\ValueObjects\RFC;
use Src\CustomerManagement\Domain\ValueObjects\Email;
use Src\CustomerManagement\Domain\ValueObjects\PhoneNumber;
use Src\CustomerManagement\Domain\ValueObjects\Address;

class Customer
{
    private ?int $id;
    private string $name;
    private RFC $rfc;
    private Address $address;
    private PhoneNumber $phone;
    private Email $email;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        ?int $id,
        string $name,
        RFC $rfc,
        Address $address,
        PhoneNumber $phone,
        Email $email,
        \DateTime $createdAt,
        \DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->rfc = $rfc;
        $this->address = $address;
        $this->phone = $phone;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        string $name,
        RFC $rfc,
        Address $address,
        PhoneNumber $phone,
        Email $email
    ): self {
        $now = new \DateTime();
        return new self(
            null,
            $name,
            $rfc,
            $address,
            $phone,
            $email,
            $now,
            $now
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRfc(): RFC
    {
        return $this->rfc;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getPhone(): PhoneNumber
    {
        return $this->phone;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function update(
        string $name,
        RFC $rfc,
        Address $address,
        PhoneNumber $phone,
        Email $email
    ): void {
        $this->name = $name;
        $this->rfc = $rfc;
        $this->address = $address;
        $this->phone = $phone;
        $this->email = $email;
        $this->updatedAt = new \DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rfc' => $this->rfc->getValue(),
            'address' => $this->address->getValue(),
            'phone' => $this->phone->getValue(),
            'email' => $this->email->getValue(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}