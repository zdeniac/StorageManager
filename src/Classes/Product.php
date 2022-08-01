<?php
declare(strict_types=1);

namespace Classes;

use Interfaces\BrandInterface;
use Interfaces\ProductInterface;

class Product implements ProductInterface
{
    public function __construct(
        public readonly string $itemNumber,
        public readonly string $name,
        public readonly float $price,
        public readonly int $id,
        public readonly BrandInterface $brand,
        private ?array $attributes = null,
    )
    {}

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }
}