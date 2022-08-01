<?php
declare(strict_types=1);

namespace Interfaces;

interface WarehouseInterface
{
    public function add(ProductInterface $product, int $quantity): bool;
    public function setCurrentCapacity(int $amount): void;
    // public function remove(): int;
}