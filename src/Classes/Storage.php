<?php
declare(strict_types=1);

namespace Classes;

use Exceptions\ProductNotFoundException;
use Exceptions\StorageIsFullException;
use Exceptions\StorageAlreadyExistsException;
use Interfaces\ProductInterface;
use Interfaces\StorageInterface;
use Interfaces\WarehouseInterface;

class Storage implements StorageInterface
{
    private array $warehouses = [];

    // protected static array $warehouses = [];
	
	// public function __construct($child) {
	// 	array_push(self::$warehouses, $child);
	// }
    
    // WAREHOUSE
    public function add(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity
    ): bool
    {
        /**
         * @var int $currentCapacity
        */
        $currentCapacity = $warehouse->currentCapacity;

        // The given warehouse is full
        if ($currentCapacity < 1) {
            // We iterate over the warehouses until we find one
            // where we can put a product
            return $this->searchForWarehouseWithSpace($product, $quantity);
        }

        // We add the product and get back the quantity
        $added = $this->addProduct($product, $warehouse, $quantity, $currentCapacity);

        // If there are any remaining items, we add them to the next warehouses
        if ($quantity > $added)
        {
            $remainder = $quantity - $added;
            return $this->searchForWarehouseWithSpace($product, $remainder);
        }

        return true;
    }
    // Warehouse-ba!
    // returns the number of items added
    private function addProduct(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity,
        int $currentCapacity
    ): int
    {
        $added = (int) ($currentCapacity >= $quantity) ? $quantity : $currentCapacity;
        $productInStorage = $this->getProductByWarehouse($product, $warehouse);

        // If the product is not stored in the warehouse
        // We add it to it, otherwise we update its quantity
        if (is_null($productInStorage))
        {
            // We create a new attribute to the Product object
            // So we can check its quantity easier
            $product = clone $product;
            $product->quantity = $added;

            $this->warehouses[$warehouse->id]['products'][] = $product;
        }
        else
        {
            $productInStorage->quantity = $productInStorage->quantity + $added;
        }

        $newCurrentCapacity = $currentCapacity - $added;
        $warehouse->setCurrentCapacity($newCurrentCapacity);

        return $added;
    }

    // Warehouse-ba!
    private function searchForWarehouseWithSpace(ProductInterface $product, int $quantity)
    {
        foreach ($this->warehouses as $value) {
            if ($value['warehouse']->currentCapacity > 0) {
                return $this->add($product, $value['warehouse'], $quantity);
            }
        }
        // If the loop is finished it means that all of the warehouses are full
        throw new StorageIsFullException('The storage is full! ' . $quantity . ' items could not been placed.');
    }


    // Warehouse-ba!
    public function remove(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity
    ): bool
    {
        // We check if the product is in the warehouse
        $productInStorage = $this->getProductByWarehouse($product, $warehouse);

        // If not we iterate forward
        if (is_null($productInStorage)) {
            return $this->searchForItemInWarehouses($product, $warehouse, $quantity);
        }

        // Otherwise we remove the product from the warehouse
        $removed = $this->removeProduct($productInStorage, $warehouse, $quantity);

        // If there are any remainder we iterate over the other warehouses
        if ($quantity > $removed)
        {
            $remainder = $quantity - $removed;

            return $this->searchForItemInWarehouses($product, $warehouse, $remainder);
        }

        return true;
    }

    // Iterates over the warehouses until it finds a warehouse where the product is stored
    // Or throws an exception
    private function searchForItemInWarehouses(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity
    ): bool
    {
        foreach ($this->warehouses as $value) {
            if ($this->getProductByWarehouse($product, $value['warehouse'])) {
                return $this->remove($product, $value['warehouse'], $quantity);
            }
        }
        // If the loop is finished it means the product is not in storage
        throw new ProductNotFoundException('The product ' . $product->name . ' is not found in the storage.');
    }


    private function removeProduct(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity,
    ): int|null
    {
        $storedProductQty = $product->quantity;
        $toRemove = (int) ($storedProductQty >= $quantity) ? $quantity : $storedProductQty;

        $newCurrentCapacity = ($storedProductQty >= $quantity) ? 
            $warehouse->currentCapacity - $quantity : $storedProductQty - $warehouse->currentCapacity;

        $warehouse->setCurrentCapacity($newCurrentCapacity);

        // If the warehouse has enough products to remove
        // We remove it
        if ($toRemove >= $storedProductQty) {
            $this->removeProductFromWarehouse($product, $warehouse);
        }

        // Or we change its quantity
        if ($toRemove < $storedProductQty) {
            $this->removeProductFromWarehouse($product, $warehouse, $storedProductQty - $toRemove);
        }

        return $toRemove;
    }

    public function removeProductFromWarehouse(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        ?int $quantity = null
    ): int|null
    {
        foreach ($this->warehouses[$warehouse->id]['products'] as $key => $item)
        {
            if ($product->id == $item->id)
            {
                // If the quantity is given, we remove the amount
                if (! is_null($quantity)) {
                    $item->quantity = $quantity;
                }
                // Else we remove the product completley
                else {
                    unset($this->warehouses[$warehouse->id]['products'][$key]);
                }

                return $quantity ?? null;
            }
        }
    }

    // Return the product from the warehouse's stock
    public function getProductByWarehouse(
        ProductInterface $product,
        WarehouseInterface $warehouse
    ): ProductInterface|null
    {
        $warehouses = $this->warehouses;

        foreach ($warehouses[$warehouse->id]['products'] as $key => $prod)
        {
            if ($prod->id == $product->id) {
                return $prod;
            }
        }

        return null;
    }

    // We create the storage for the warehouse
    // The Storage class has to know about every existing warehouse
    // PARENT KONSTRUKTOR
    public function create(WarehouseInterface $warehouse): void
    {
        $warehouses = $this->warehouses;
        if (array_key_exists($warehouse->id, $warehouses)) {
            throw new StorageAlreadyExistsException('Storage for warehouse #' . $warehouse->id . ' already exists.');
        }
        // megnézzük, van-e kulcs duplikáció, ha van, exception
        $warehouses[$warehouse->id] = [
            'warehouse' => $warehouse,
            'products' => []
        ];

        $this->warehouses = $warehouses;
    }

    public function getStorageByWarehouse(WarehouseInterface $warehouse): array
    {
        return $this->warehouses[$warehouse->id]['products'];
    }

}