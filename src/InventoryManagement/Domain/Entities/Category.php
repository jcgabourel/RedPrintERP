<?php

namespace Src\InventoryManagement\Domain\Entities;

use Src\InventoryManagement\Domain\ValueObjects\CategoryId;
use InvalidArgumentException;

class Category
{
    private CategoryId $id;
    private string $name;
    private string $description;
    private ?CategoryId $parentId;
    private int $level;
    private string $path;
    private bool $isActive;
    private int $productCount;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        CategoryId $id,
        string $name,
        string $description,
        ?CategoryId $parentId,
        int $level,
        string $path,
        bool $isActive,
        int $productCount,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->validateName($name);
        $this->validateDescription($description);
        $this->validateLevel($level);
        $this->validatePath($path);
        $this->validateProductCount($productCount);

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->parentId = $parentId;
        $this->level = $level;
        $this->path = $path;
        $this->isActive = $isActive;
        $this->productCount = $productCount;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Category name cannot be empty');
        }

        if (strlen($name) > 100) {
            throw new InvalidArgumentException('Category name cannot exceed 100 characters');
        }
    }

    private function validateDescription(string $description): void
    {
        if (strlen($description) > 500) {
            throw new InvalidArgumentException('Category description cannot exceed 500 characters');
        }
    }

    private function validateLevel(int $level): void
    {
        if ($level < 0) {
            throw new InvalidArgumentException('Category level cannot be negative');
        }

        if ($level > 10) {
            throw new InvalidArgumentException('Category level cannot exceed 10');
        }
    }

    private function validatePath(string $path): void
    {
        if (empty($path)) {
            throw new InvalidArgumentException('Category path cannot be empty');
        }

        if (strlen($path) > 255) {
            throw new InvalidArgumentException('Category path cannot exceed 255 characters');
        }
    }

    private function validateProductCount(int $productCount): void
    {
        if ($productCount < 0) {
            throw new InvalidArgumentException('Product count cannot be negative');
        }
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getParentId(): ?CategoryId
    {
        return $this->parentId;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getProductCount(): int
    {
        return $this->productCount;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): void
    {
        $this->validateName($name);
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateDescription(string $description): void
    {
        $this->validateDescription($description);
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function incrementProductCount(): void
    {
        $this->productCount++;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function decrementProductCount(): void
    {
        if ($this->productCount > 0) {
            $this->productCount--;
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function isRoot(): bool
    {
        return $this->parentId === null || $this->parentId->isNull();
    }

    public function hasChildren(): bool
    {
        // Esta lógica se implementaría en el repositorio
        return false;
    }

    public function getAncestorIds(): array
    {
        if (empty($this->path)) {
            return [];
        }

        $ids = explode('/', trim($this->path, '/'));
        return array_map('intval', $ids);
    }

    public function isDescendantOf(CategoryId $categoryId): bool
    {
        return in_array($categoryId->getValue(), $this->getAncestorIds(), true);
    }

    public function isAncestorOf(Category $other): bool
    {
        return $other->isDescendantOf($this->id);
    }

    public static function create(
        string $name,
        string $description,
        ?CategoryId $parentId = null
    ): self {
        $id = new CategoryId(null); // ID será asignado por el repositorio
        $level = $parentId ? 1 : 0; // Nivel se calculará en el repositorio
        $path = ''; // Path se calculará en el repositorio

        return new self(
            $id,
            $name,
            $description,
            $parentId,
            $level,
            $path,
            true,
            0,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parentId?->getValue(),
            'level' => $this->level,
            'path' => $this->path,
            'is_active' => $this->isActive,
            'product_count' => $this->productCount,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }
}