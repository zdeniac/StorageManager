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
        name:'Cipő 1', 
        price: 45000,
        id: 1,
        brand: $nike
    );
    
    $product2 = new Product(
        itemNumber: '3303', 
        name:'Cipő 2', 
        price: 15000,
        id: 3,
        brand: $nike,
        attributes: ['productionDate' => date('Y-m-d H:i:s')]
    );
    
    $product3 = new Product(
        itemNumber: '54353', 
        name:'Cipő 3', 
        price: 9999,
        id: 15,
        brand: $vans
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

    $storage->assign($warehouse, $warehouse2, $warehouse3);
    
    $storage->add(warehouse: $warehouse, product: $product, quantity: 70);

    dump($storage);

}
catch (\Exception $e) {
    echo $e->getMessage();
}