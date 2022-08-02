<?php
declare(strict_types=1);

namespace Classes;

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
    )
    {
        // Product és a quantity kell

        // Üres raktár rekurzív újrahívás
            // Ha minden raktár üres Exception
        // $currentCapacity = $warehouse->currentCapacity;
        // $capacity = $warehouse->capacity

        // Ha üres a raktár megyünk a következő raktárra, ami nem üres
        // if ($warehouse->isEmpty() ) {
        //     return $this->searchForItemInWarehouses($product);
        // }

        // Megnézzük, hogy a nem üres raktárban szerepel-e a termék
        //$productInStorage = $this->getProductByWarehouse($product, $warehouse);

        // Ha nem, újrahívjuk a függvényt egy másik raktárral
        // if (is_null($product)) {
        //  return $this->searchForItemInWarehouses($product);
        // }
        // Szerepel annyi termék a raktárban? boolean|raktár

        // Ha szerepel, kivesszük a raktárból
        // if ($productInStorage->quantity >= $quantity) {
        //     $this->removeProduct($product, $quantity);
        // }

            // Ha nem rekuzív újrahívás
                // Ha egyik raktárban sem szerepel a termék, Exception
            // Ha igen, kiveszem a tételt

        // Van maradék?
            // Ha nincs, visszatérés bool
            // Ha van, rekurzív újrahívás
    }

    // private function searchForItemInWarehouses(ProductInterface $product)
    // {
    //     foreach ($this->warehouses as $value) {
    //         if ($this->getProductByWarehouse($product, $value['warehouse'])) {
    //             return $this->remove($product, $value['warehouse']);
    //         }
    //     }
    //     // If the loop is finished it means the product is not in storage
    //     throw new ProductNotFoundException('The product ' . $product->name . ' is not found in the storage.');
    // }


    private function removeProduct()
    {
        // $added = (int) ($currentCapacity >= $quantity) ? $quantity : $currentCapacity;
        // $productInStorage = $this->getProductByWarehouse($product, $warehouse);

        // // If the product is not stored in the warehouse
        // // We add it to it, otherwise we update its quantity
        // if (is_null($productInStorage))
        // {
        //     // We create a new attribute to the Product object
        //     // So we can check its quantity easier
        //     $product = clone $product;
        //     $product->quantity = $added;

        //     $this->warehouses[$warehouse->id]['products'][] = $product;
        // }
        // else
        // {
        //     $productInStorage->quantity = $productInStorage->quantity + $added;
        // }

        // $newCurrentCapacity = $currentCapacity - $added;
        // $warehouse->setCurrentCapacity($newCurrentCapacity);

        // return $added;
    }

    
    
    
    
    //public function addProductToWarehouse()

    // public function getAll(): array
    // {
    //     // return self::$warehouses;
    // }

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