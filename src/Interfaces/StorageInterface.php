<?php
declare(strict_types=1);

namespace Interfaces;

use Exceptions\WarehouseNotAssignedException;

interface StorageInterface
{
    public function add(ProductInterface $product, WarehouseInterface $warehouse, int $quantity): bool;
    public function remove(ProductInterface $product, WarehouseInterface $warehouse, int $quantity): bool;
    public function assign(WarehouseInterface ...$warehouses): void;
    public function getStorageByWarehouse(WarehouseInterface $warehouse): array|WarehouseNotAssignedException;
    public function getProductByWarehouse(ProductInterface $product, WarehouseInterface $warehouse): ProductInterface|null;
    public function getContent(): array;
}