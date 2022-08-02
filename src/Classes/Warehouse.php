<?php
declare(strict_types=1);

namespace Classes;

use Interfaces\WarehouseInterface;
use Interfaces\ProductInterface;
use Interfaces\StorageInterface;
use phpDocumentor\Reflection\Types\Boolean;

class Warehouse implements WarehouseInterface
{
    public int $currentCapacity;

    // A konstruktorban meghívjuk a szülő konstruktorát
    public function __construct(
        public readonly string $name,
        public readonly string $address,
        public readonly int $capacity,
        public readonly int $id,
        private StorageInterface $storage,
        private ?array $attributes = null,
    )
    {
        $this->currentCapacity = $capacity;
        $this->storage->create($this);
    }

    public function add(
        ProductInterface $product,
        int $quantity
    ): bool
    {
        return $this->storage->add($product, $this, $quantity);
    }

    // returns the whole storage of the warehouse
    public function getStorage(): array
    {
        return $this->storage->getStorageByWarehouse($this);
    }

    public function setCurrentCapacity(int $amount): void
    {
        $this->currentCapacity = $amount;
    }

    public function isEmpty(): bool
    {
        return $this->currentCapacity == $this->capacity ? true : false;
    }

    public function isFull(): bool
    {
        return $this->currentCapacity == 0 ? true : false;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

}