<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->group('api', function($routes) {
    $routes->get('produits', 'Api\ProduitController::index');
    $routes->get('produits/(:num)', 'Api\ProduitController::show/$1');   // ← AJOUT
    $routes->post('produits', 'Api\ProduitController::create');
    $routes->put('produits/(:num)', 'Api\ProduitController::update/$1');
    $routes->delete('produits/(:num)', 'Api\ProduitController::delete/$1');
    $routes->post('dlc/calculer', 'Api\DlcController::calculer');
});

$routes->get('dlc/catalogue', 'Dlc::catalogue');
$routes->get('dlc/calculateur', 'Dlc::calculateur');