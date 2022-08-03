<?php
declare(strict_types=1);

use Classes\Brand;
use \PHPUnit\Framework\TestCase as TestCase;

class BrandTest extends TestCase
{
    public function test_out_of_range_exception_is_thrown()
    {
        $this->expectException(Exceptions\BrandQualityOutOfRangeException::class);

        $brand = new Brand('Márka', rand(5, 100));
    }

    public function test_attributes_are_set()
    {
        $brand = new Brand('Márka', 1, ['length' => 10]);

        $this->assertSame($brand->length, 10);
    }
}