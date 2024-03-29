<?php
declare(strict_types=1);

namespace Interfaces;

interface WarehouseInterface
{
    public function setCurrentCapacity(int $amount): void;
    public function isEmpty(): bool;
    public function isFull(): bool;
}