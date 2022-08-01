<?php
declare(strict_types=1);

use Classes\Product;
use Classes\Storage;
use Classes\Warehouse;
use \PHPUnit\Framework\TestCase as TestCase;

class StorageTest extends TestCase
{
    public function test_storage_is_correct()
    {
        $storage = new Storage();

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1
        );
    
        $product2 = new Product(
            itemNumber: '3303', 
            name:'Autó', 
            price: 1000,
            id: 3
        );

        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
            storage: $storage
        );
        
        $warehouse2 = new Warehouse(
            name: 'Raktár 2', 
            address: 'Budapest', 
            capacity: 20,
            id: 300,
            storage: $storage
        );

        $warehouse->add(product: $product, quantity: 5);
        $warehouse->add(product: $product2, quantity: 10);

        // $this->assert
    }
}