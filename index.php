<?php

require('vendor/autoload.php');

use Classes\Brand;
use Classes\Warehouse;
use Classes\Product;
use Classes\Storage;

try {
    $storage = new Storage();

    $nike = new Brand('Nike', 1);
    $vans = new Brand('Vans', 2);

    $product = new Product(
        itemNumber: '2121', 
        name:'Vans cipő', 
        price: 200.30,
        id: 1,
        brand: $nike
    );
    
    $product2 = new Product(
        itemNumber: '3303', 
        name:'Autó', 
        price: 1000,
        id: 3,
        brand: $nike,
        attributes: ['productionDate' => date('Y-m-d H:i:s')]
    );
    
    $product3 = new Product(
        itemNumber: '54353', 
        name:'Ház', 
        price: 9999,
        id: 15,
        brand: $vans
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
        storage: $storage,
        attributes: ['country' => 'Hungary']
    );

    $warehouse3 = new Warehouse(
        name: 'Raktár 3', 
        address: 'Budapest', 
        capacity: 40,
        id: 111,
        storage: $storage
    );
    
    
    $warehouse->add(product: $product, quantity: 70);
    $warehouse->remove(product: $product, quantity: 70);

    dump($storage);

}
catch (\Exception $e) {
    echo $e->getMessage();
}