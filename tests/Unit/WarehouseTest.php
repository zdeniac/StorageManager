<?php
declare(strict_types=1);

use Classes\Warehouse;
use \PHPUnit\Framework\TestCase as TestCase;

class WarehouseTest extends TestCase
{
    public function test_attributes_are_set()
    {
        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
            attributes: ['country' => 'Hungary']
        );
    
        $this->assertSame($warehouse->country, 'Hungary');
    }

    public function test_current_capacity_is_correct_on_creation()
    {
        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
            attributes: ['country' => 'Hungary']
        );
    
        $this->assertSame($warehouse->currentCapacity, 10);
    }

    public function test_current_capacity_is_set()
    {
        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
            attributes: ['country' => 'Hungary']
        );

        $warehouse->setCurrentCapacity(5);
    
        $this->assertSame($warehouse->currentCapacity, 5);

    }

    public function test_is_full_is_correct()
    {
        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
            attributes: ['country' => 'Hungary']
        );

        $warehouse->setCurrentCapacity(0);
    
        $this->assertTrue($warehouse->isFull());
    }
 
    public function test_is_empty_is_correct()
    {
        $warehouse = new Warehouse(
            name: 'Raktár 1', 
            address: '7761 Kozármisleny Székely B. 35', 
            capacity: 10,
            id: 200,
            attributes: ['country' => 'Hungary']
        );
            
        $this->assertTrue($warehouse->isEmpty());
    }

}