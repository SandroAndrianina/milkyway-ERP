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
    $routes->get('ecoulement/produits', 'Api\EcoulementProduitController::index');
    $routes->get('ecoulement/produits/(:num)', 'Api\EcoulementProduitController::show/$1');
    $routes->post('ecoulement/produits', 'Api\EcoulementProduitController::create');
    $routes->put('ecoulement/produits/(:num)', 'Api\EcoulementProduitController::update/$1');
    $routes->delete('ecoulement/produits/(:num)', 'Api\EcoulementProduitController::delete/$1');

});

//DLC
$routes->get('dlc/catalogue', 'Dlc::catalogue');
$routes->get('dlc/calculateur', 'Dlc::calculateur');

//ecoulement
$routes->get('produits-ecoulement', 'EcoulementProduit::index');