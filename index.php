<?php

require('vendor/autoload.php');

use Classes\Warehouse;
use Classes\Product;
use Classes\Storage;

try {
    $storage = new Storage();
    // MÁRKA HIÁNYZIK
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
    
    $product3 = new Product(
        itemNumber: '54353', 
        name:'Ház', 
        price: 9999,
        id: 15
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

    $warehouse3 = new Warehouse(
        name: 'Raktár 3', 
        address: 'Budapest', 
        capacity: 40,
        id: 111,
        storage: $storage
    );
    
    
    $warehouse->add(product: $product, quantity: 100);
    dump($warehouse3);
}
catch (\Exception $e) {
    echo $e->getMessage();
}