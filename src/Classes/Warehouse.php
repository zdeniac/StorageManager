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
        private ?array $attributes = null,
    )
    {
        $this->currentCapacity = $capacity;
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

    public function __set(string $name, $value)
    {
        return $this->attributes[$name] = $value;
    }
}