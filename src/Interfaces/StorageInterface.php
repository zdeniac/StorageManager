<?php
declare(strict_types=1);

namespace Interfaces;

interface StorageInterface
{
    public function add(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity
    ): bool;
    public function create(WarehouseInterface $warehouse): void;
    public function getStorageByWarehouse(WarehouseInterface $warehouse): array;
    public function getProductByWarehouse(
        ProductInterface $product,
        WarehouseInterface $warehouse
    ): ProductInterface|null;
    public function remove(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity
    ): bool;

}