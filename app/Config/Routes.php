<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('api', function($routes) {

    //DLC
    $routes->get('produits', 'Api\ProduitController::index');
    $routes->get('produits/(:num)', 'Api\ProduitController::show/$1');   // ← AJOUT
    $routes->post('produits', 'Api\ProduitController::create');
    $routes->put('produits/(:num)', 'Api\ProduitController::update/$1');
    $routes->delete('produits/(:num)', 'Api\ProduitController::delete/$1');
    $routes->post('dlc/calculer', 'Api\DlcController::calculer');

    //ecoulement

    //produits
    $routes->get('ecoulement/produits', 'Api\EcoulementProduitController::index');
    $routes->get('ecoulement/produits/(:num)', 'Api\EcoulementProduitController::show/$1');
    $routes->post('ecoulement/produits', 'Api\EcoulementProduitController::create');
    $routes->put('ecoulement/produits/(:num)', 'Api\EcoulementProduitController::update/$1');
    $routes->delete('ecoulement/produits/(:num)', 'Api\EcoulementProduitController::delete/$1');

    // Clients
    $routes->get('ecoulement/clients', 'Api\ClientController::index');
    $routes->get('ecoulement/clients/(:num)', 'Api\ClientController::show/$1');
    $routes->post('ecoulement/clients', 'Api\ClientController::create');
    $routes->put('ecoulement/clients/(:num)', 'Api\ClientController::update/$1');
    $routes->delete('ecoulement/clients/(:num)', 'Api\ClientController::delete/$1');

});

//DLC
$routes->get('dlc/catalogue', 'Dlc::catalogue');
$routes->get('dlc/calculateur', 'Dlc::calculateur');

//ecoulement
$routes->get('produits-ecoulement', 'EcoulementProduit::index');
$routes->get('clients', 'Clients::index');