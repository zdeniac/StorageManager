<?php
declare(strict_types=1);

use Classes\Product;
use Classes\Storage;
use Classes\Warehouse;
use \PHPUnit\Framework\TestCase as TestCase;

class StorageTest extends TestCase
{
    public function test_product_is_added_to_the_given_warehouse()
    {
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
            brand: $brand
        );

        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
        );

        $storage = new Storage();

        $storage->assign($warehouse);
        $storage->add($product, $warehouse, 10);

        $this->assertSame($product->id, $storage->getStorageByWarehouse($warehouse)[0]->id);
    }

    public function test_product_quantity_is_added_to_the_given_warehouse()
    {
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
            brand: $brand
        );

        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
        );

        $storage = new Storage();

        $storage->assign($warehouse);
        $storage->add($product, $warehouse, 10);

        $this->assertSame(10, $storage->getStorageByWarehouse($warehouse)[0]->quantity);
    }

    public function test_product_is_added_to_correct_warehouse_if_given_is_full()
    {
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
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
            address: '7761 Kozármisleny Székely B. 33', 
            capacity: 10,
            id: 9,
        );

        $warehouse->setCurrentCapacity(0);
    
        $storage = new Storage();

        $storage->assign($warehouse, $warehouse2);
        $storage->add($product, $warehouse, 10);

        $this->assertSame($product->id, $storage->getStorageByWarehouse($warehouse2)[0]->id);
   }

    public function test_product_quantity_is_added_to_correct_warehouse_if_given_is_full()
    {
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
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
            address: '7761 Kozármisleny Székely B. 33', 
            capacity: 10,
            id: 9,
        );

        $warehouse->setCurrentCapacity(0);
    
        $storage = new Storage();

        $storage->assign($warehouse, $warehouse2);
        $storage->add($product, $warehouse, 10);

        $this->assertSame(10, $storage->getStorageByWarehouse($warehouse2)[0]->quantity);
    }

    public function test_storage_is_full_exception_is_thrown()
    {
        $this->expectException(Exceptions\StorageIsFullException::class);
        
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
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
            address: '7761 Kozármisleny Székely B. 33', 
            capacity: 10,
            id: 9,
        );

        $warehouse->setCurrentCapacity(0);
        $warehouse2->setCurrentCapacity(0);

        $storage = new Storage();

        $storage->assign($warehouse, $warehouse2);
        $storage->add($product, $warehouse, 10);
    }

    public function test_added_products_are_separated_correctly()
    {
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
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
            address: 'Budapest', 
            capacity: 20,
            id: 300,
            attributes: ['country' => 'Hungary']
        );
    
        $warehouse3 = new Warehouse(
            name: 'Raktár 3', 
            address: 'Budapest', 
            capacity: 40,
            id: 111,
        );

        $storage = new Storage();

        $storage->assign($warehouse, $warehouse2, $warehouse3);
        $storage->add(warehouse: $warehouse, product: $product, quantity: 55);

        $this->assertSame(10, $storage->getStorageByWarehouse($warehouse)[0]->quantity);
        $this->assertSame(20, $storage->getStorageByWarehouse($warehouse2)[0]->quantity);  
        $this->assertSame(25, $storage->getStorageByWarehouse($warehouse3)[0]->quantity);    
    }

    public function test_warehouse_is_not_assigned_exception_is_thrown()
    {
        $this->expectException(Exceptions\WarehouseNotAssignedException::class);
        
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
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
            address: '7761 Kozármisleny Székely B. 33', 
            capacity: 10,
            id: 9,
        );

        $storage = new Storage();

        $storage->assign($warehouse);
        $storage->add($product, $warehouse2, 10);
    }

    public function test_product_is_removed()
    {        
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
            brand: $brand
        );

        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
        );

        $storage = new Storage();

        $storage->assign($warehouse);
        $storage->add($product, $warehouse, 10);

        $storage->remove($product, $warehouse);

        $this->assertEmpty($storage->getStorageByWarehouse($warehouse));
    }

    public function test_product_quantity_is_removed()
    {        
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
            brand: $brand
        );

        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
        );

        $storage = new Storage();

        $storage->assign($warehouse);
        $storage->add($product, $warehouse, 10);

        $storage->remove($product, $warehouse, 5);

        $storedProduct = $storage->getProductByWarehouse($product, $warehouse);

        $this->assertEquals($storedProduct->quantity, 5);
    }

    public function test_correct_product_is_removed()
    {        
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
            brand: $brand
        );

        $product2 = new Product(
            itemNumber: '1111', 
            name:'Vans cipő 2', 
            price: 110.30,
            id: 3,
            brand: $brand
        );

        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
        );

        $storage = new Storage();

        $storage->assign($warehouse);

        $storage->add($product, $warehouse, 5);
        $storage->add($product2, $warehouse, 5);

        $storage->remove($product, $warehouse);

        $content = $storage->getStorageByWarehouse($warehouse);

        $this->assertFalse(in_array($product, $content));
    }

    public function test_null_is_returned_when_product_is_not_in_stored()
    {        
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
            brand: $brand
        );

        $product2 = new Product(
            itemNumber: '1111', 
            name:'Vans cipő 2', 
            price: 110.30,
            id: 3,
            brand: $brand
        );

        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
        );

        $storage = new Storage();

        $storage->assign($warehouse);

        $storage->add($product, $warehouse, 5);
        $storage->add($product2, $warehouse, 5);

        $storage->remove($product, $warehouse);

        $this->assertNull($storage->getProductByWarehouse($product, $warehouse));
    }

    public function test_product_is_removed_from_correct_warehouse()
    {        
        $brand = $this->createMock(Classes\Brand::class);

        $product = new Product(
            itemNumber: '2121', 
            name:'Vans cipő', 
            price: 200.30,
            id: 1,
            brand: $brand
        );

        $product2 = new Product(
            itemNumber: '2222', 
            name:'Vans cipő2', 
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

        $storage->remove($product2, $warehouse);

        $this->assertTrue($warehouse->isFull());
        $this->assertEmpty($storage->getStorageByWarehouse($warehouse2));
    }

}