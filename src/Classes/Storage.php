<?php
declare(strict_types=1);

namespace Classes;

use Exceptions\ProductNotFoundException;
use Exceptions\StorageIsFullException;
use Exceptions\StorageAlreadyExistsException;
use Exceptions\WarehouseNotAssignedException;
use Interfaces\ProductInterface;
use Interfaces\StorageInterface;
use Interfaces\WarehouseInterface;

class Storage implements StorageInterface
{
    private array $warehouses = [];

    /**
     * Add a product to the warehouse.
     * If the warehouse is full, or the number of products cannot be added,
     * the function is recalled recursively with another warehouse.
     */
    public function add(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity
    ): bool
    {
        if (! array_key_exists($warehouse->id, $this->warehouses)) {
            throw new WarehouseNotAssignedException('The warehouse #' . $warehouse->id . ' is not assigned to the storage.');
        }

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
            // This would be better as a separate class, like "WarehouseItem"
            $product = clone $product;
            $product->quantity = $added;

            $this->warehouses[$warehouse->id]['products'][] = $product;
        }
        else
        {
            $productInStorage->quantity = $productInStorage->quantity + $added;
        }

        // We update the warehouse's current capacity
        $newCurrentCapacity = $currentCapacity - $added;
        $warehouse->setCurrentCapacity($newCurrentCapacity);

        return $added;
    }

    /**
     * Iterate over the warehouses until we find a warehouse with at least one space,
     * and recall add() with the remaining quantity,
     * or throw exception.
     * @throws StorageIsFullException
     */ 
    private function searchForWarehouseWithSpace(ProductInterface $product, int $quantity)
    {
        // We iterate over the warehouses until we find one with free space
        foreach ($this->warehouses as $value) {
            if (! $value['warehouse']->isFull()) {
                return $this->add($product, $value['warehouse'], $quantity);
            }
        }
        // If the loop is finished it means that all of the warehouses are full
        throw new StorageIsFullException('The storage is full! ' . $quantity . ' items could not been placed.');
    }

    /**
     * Remove a product from the warehouse.
     * If the warehouse is empty, or the product is not stored in the given warehouse,
     * the function is recalled recursively with another warehouse.
     */
    public function remove(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity
    ): bool
    {
        if (! array_key_exists($warehouse->id, $this->warehouses)) {
            throw new WarehouseNotAssignedException('The warehouse #' . $warehouse->id . ' is not assigned to the storage.');
        }

        // We check if the product is in the warehouse
        $productInStorage = $this->getProductByWarehouse($product, $warehouse);

        // If not we iterate forward
        if (is_null($productInStorage)) {
            return $this->searchForItemInWarehouses($product, $warehouse, $quantity);
        }

        // Otherwise we remove the product from the warehouse
        $removed = $this->removeProduct($productInStorage, $warehouse, $quantity);

        // If we could not remove the given quantity from the given warehouse
        // we iterate over the other warehouses
        if ($quantity > $removed)
        {
            $remainder = $quantity - $removed;

            return $this->searchForItemInWarehouses($product, $warehouse, $remainder);
        }

        return true;
    }

    /**
     * Iterate over the warehouses until we find a warehouse where the product is stored,
     * and recall remove() with the remaining quantity,
     * or throw exception.
     * @throws ProductNotFoundException
     */ 
    private function searchForItemInWarehouses(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        int $quantity
    ): bool
    {
        foreach ($this->warehouses as $value) {
            // If the product is found in the warehouse
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

        // We recalibrate the current capacity according to the quantity to be removed
        $newCurrentCapacity = ($storedProductQty >= $quantity) ? 
            $warehouse->currentCapacity - $quantity : $storedProductQty - $warehouse->currentCapacity;

        $warehouse->setCurrentCapacity($newCurrentCapacity);

        // If the warehouse has enough products to remove
        // We remove all of them
        if ($toRemove >= $storedProductQty) {
            $this->removeItemFromWarehouse($product, $warehouse);
        }

        // Otherwise we change the quantity
        if ($toRemove < $storedProductQty) {
            $this->removeItemFromWarehouse($product, $warehouse, $storedProductQty - $toRemove);
        }

        return $toRemove;
    }

    private function removeItemFromWarehouse(
        ProductInterface $product,
        WarehouseInterface $warehouse,
        ?int $quantity = null
    ): int|null
    {
        foreach ($this->warehouses[$warehouse->id]['products'] as $key => $item)
        {
            if ($product->id == $item->id)
            {
                // If the quantity is given, that means we don't remove all of it
                if (! is_null($quantity)) {
                    $item->quantity = $quantity;
                }
                // Else we remove the product completeley
                else {
                    unset($this->warehouses[$warehouse->id]['products'][$key]);
                }

                return $quantity ?? null;
            }
        }
    }

    /**
     * Return the product from the warehouse's stock.
     */ 
    public function getProductByWarehouse(
        ProductInterface $product,
        WarehouseInterface $warehouse
    ): ProductInterface|null
    {
        if (! array_key_exists($warehouse->id, $this->warehouses)) {
            throw new WarehouseNotAssignedException('The warehouse #' . $warehouse->id . ' is not assigned to the storage.');
        }

        $warehouses = $this->warehouses;

        foreach ($warehouses[$warehouse->id]['products'] as $key => $prod) {
            if ($prod->id == $product->id) {
                return $prod;
            }
        }

        return null;
    }

    /**
     * Assign the warehouse to the stock.
     * @throws StorageAlreadyExistsException
     */
    public function assign(WarehouseInterface ...$warehouses): void
    {
        $assignedWarehouses = $this->warehouses;

        foreach ($warehouses as $warehouse)
        {
            if (array_key_exists($warehouse->id, $assignedWarehouses)) {
                throw new StorageAlreadyExistsException('Storage for warehouse #' . $warehouse->id . ' already exists.');
            }
            
            $assignedWarehouses[$warehouse->id] = [
                'warehouse' => $warehouse,
                'products' => []
            ];    
        }

        $this->warehouses = $assignedWarehouses;
    }
    /**
     * Get the whole storage of a warehouse.
     */
    public function getStorageByWarehouse(WarehouseInterface $warehouse): array
    {
        return $this->warehouses[$warehouse->id]['products'];
    }

}