<?php
declare(strict_types=1);

use Classes\Product;
use \PHPUnit\Framework\TestCase as TestCase;

class ProductTest extends TestCase
{
    public function test_attributes_are_set()
    {
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
            brand: $brand,
            attributes: ['countryOfOrigin' => 'China']
        );
    
        $this->assertSame($product->countryOfOrigin, 'China');
    }
}