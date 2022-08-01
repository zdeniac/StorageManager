<?php
declare(strict_types=1);

namespace Classes;

use Interfaces\WarehouseInterface;
use Interfaces\ProductInterface;
use Classes\Storage;
use Interfaces\StorageInterface;

class Warehouse implements WarehouseInterface
{
    public int $currentCapacity;

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

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

}