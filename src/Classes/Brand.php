<?php
declare(strict_types=1);

namespace Classes;

use Exceptions\BrandQualityOutOfRangeException;
use Interfaces\BrandInterface;

class Brand implements BrandInterface
{
    private const QUALITY_MIN = 1;
    private const QUALITY_MAX = 5;

    public function __construct(
        public readonly string $name,
        public readonly int $quality,
        private ?array $attributes = null,
    )
    {
        if ($quality < self::QUALITY_MIN || $quality > self::QUALITY_MAX) {
            throw new BrandQualityOutOfRangeException("The brand's quality must be between " . self::QUALITY_MIN . " and " .self::QUALITY_MAX);
        }
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