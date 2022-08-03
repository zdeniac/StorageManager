<?php
declare(strict_types=1);

namespace Classes;

use Interfaces\WarehouseInterface;
use Interfaces\ProductInterface;
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

        // Anytime a warehouse is created, we assign it to the stock
        $this->storage->assign($this);
    }

    public function add(
        ProductInterface $product,
        int $quantity
    ): bool
    {
        return $this->storage->add($product, $this, $quantity);
    }

    public function remove(ProductInterface $product, int $quantity): bool
    {
        return $this->storage->remove($product, $this, $quantity);
    }

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