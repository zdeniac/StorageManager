<?php
declare(strict_types=1);

use Classes\Warehouse;
use Classes\Storage;
use Classes\Product;
use Classes\Brand;
use \PHPUnit\Framework\TestCase as TestCase;

class WarehouseStorageTest extends TestCase
{
    public function test_current_capacity_is_calculated_correctly()
    {
        $brand = new Brand('Nike', 1);

        $product = new Product(
            itemNumber: '2121', 
            name:'Cipő 1', 
            price: 200.30,
            id: 1,
            brand: $brand
        );

        $product2 = new Product(
            itemNumber: '2222', 
            name:'Cipő 2', 
            price: 200,
            id: 3,
            brand: $brand
        );

        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
        );

        $warehouse2 = new Warehouse(
            name: 'Raktár 2', 
            address: '7761 Kozármisleny Székely B. 34', 
            capacity: 10,
            id: 100,
        );

        $storage = new Storage();

        $storage->assign($warehouse, $warehouse2);
        $storage->add($product, $warehouse, 10);
        $storage->add($product2, $warehouse2, 10);

        $this->assertSame(0, $warehouse->currentCapacity);
        $this->assertSame(0, $warehouse2->currentCapacity);

        $storage->remove($product2, $warehouse, 5);
        
        $this->assertSame(0, $warehouse->currentCapacity);
        $this->assertSame(5, $warehouse2->currentCapacity);

        $storage->remove($product2, $warehouse2, 3);

        $this->assertSame(8, $warehouse2->currentCapacity);

        $storage->add($product, $warehouse2, 4);

        $this->assertSame(4, $warehouse2->currentCapacity);
    }
}