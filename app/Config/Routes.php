<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ================================
// AUTHENTIFICATION (web)
// ================================
$routes->get('login', 'Auth::login');
$routes->post('auth/doLogin', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');
$routes->get('register', 'Auth::register');
$routes->post('auth/doRegister', 'Auth::doRegister');

// ================================
// ACCUEIL (protégé)
// ================================
$routes->get('/', 'Home::index', ['filter' => 'role:vente,stocks,admin']);

// ================================
// ADMIN (gestion utilisateurs) – rôle admin uniquement
// ================================
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    $routes->get('users', 'Admin::users');
    $routes->get('validate/(:num)', 'Admin::validateUser/$1');
});

// ================================
// DLC – rôle admin uniquement
// ================================
$routes->group('dlc', ['filter' => 'role:admin'], function($routes) {
    $routes->get('catalogue', 'Dlc::catalogue');
    $routes->get('calculateur', 'Dlc::calculateur');
});

// ================================
// ÉCOULEMENT – VENTES (rôle vente ou admin)
// ================================
$routes->group('', ['filter' => 'role:vente,admin'], function($routes) {
    $routes->get('clients', 'Clients::index');
    $routes->get('clients/details/(:num)', 'Clients::details/$1');
});

// ================================
// ÉCOULEMENT – STOCKS (rôle stocks ou admin)
// ================================
$routes->group('', ['filter' => 'role:stocks,admin'], function($routes) {
    $routes->get('produits-ecoulement', 'EcoulementProduit::index');
    $routes->get('mouvements', 'Mouvements::index');
    $routes->get('etat-stock', 'EtatStock::index');
    $routes->get('recapitulation', 'Recapitulation::index');
});

// ================================
// API – GROUPES PROTÉGÉS PAR RÔLE
// ================================
$routes->group('api', ['filter' => 'role:admin'], function($routes) {
    // DLC
    $routes->get('produits', 'Api\ProduitController::index');
    $routes->get('produits/(:num)', 'Api\ProduitController::show/$1');
    $routes->post('produits', 'Api\ProduitController::create');
    $routes->put('produits/(:num)', 'Api\ProduitController::update/$1');
    $routes->delete('produits/(:num)', 'Api\ProduitController::delete/$1');
    $routes->post('dlc/calculer', 'Api\DlcController::calculer');
});

$routes->group('api', ['filter' => 'role:vente,admin'], function($routes) {
    // Clients (CRUD + achats + exports)
    $routes->get('ecoulement/clients', 'Api\ClientController::index');
    $routes->get('ecoulement/clients/(:num)', 'Api\ClientController::show/$1');
    $routes->post('ecoulement/clients', 'Api\ClientController::create');
    $routes->put('ecoulement/clients/(:num)', 'Api\ClientController::update/$1');
    $routes->delete('ecoulement/clients/(:num)', 'Api\ClientController::delete/$1');
    $routes->get('ecoulement/clients/(:num)/achats', 'Api\ClientController::achats/$1');
    // Exports clients
    $routes->get('ecoulement/clients/export-preview', 'Api\ClientController::exportPreview');
    $routes->get('ecoulement/clients/export/csv', 'Api\ClientController::exportCsv');
    $routes->get('ecoulement/clients/export/pdf', 'Api\ClientController::exportPdf');
    // Exports achats clients
    $routes->get('ecoulement/clients/(:num)/achats/export-preview', 'Api\ClientController::exportAchatsPreview/$1');
    $routes->get('ecoulement/clients/(:num)/achats/export/csv', 'Api\ClientController::exportAchatsCsv/$1');
    $routes->get('ecoulement/clients/(:num)/achats/export/pdf', 'Api\ClientController::exportAchatsPdf/$1');
});

$routes->group('api', ['filter' => 'role:stocks,admin'], function($routes) {
    // Produits (écoulement)
    $routes->get('ecoulement/produits', 'Api\EcoulementProduitController::index');
    $routes->get('ecoulement/produits/(:num)', 'Api\EcoulementProduitController::show/$1');
    $routes->post('ecoulement/produits', 'Api\EcoulementProduitController::create');
    $routes->put('ecoulement/produits/(:num)', 'Api\EcoulementProduitController::update/$1');
    $routes->delete('ecoulement/produits/(:num)', 'Api\EcoulementProduitController::delete/$1');

    // Mouvements
    $routes->get('ecoulement/mouvements', 'Api\MouvementController::index');
    $routes->get('ecoulement/mouvements/chart', 'Api\MouvementController::chart');
    $routes->post('ecoulement/mouvements', 'Api\MouvementController::create');
    $routes->post('ecoulement/mouvements/batch', 'Api\MouvementController::createBatch');
    $routes->get('ecoulement/mouvements/(:num)', 'Api\MouvementController::show/$1');
    $routes->get('ecoulement/mouvements/export-preview', 'Api\MouvementController::exportPreview');
    $routes->get('ecoulement/mouvements/export/csv', 'Api\MouvementController::exportCsv');
    $routes->get('ecoulement/mouvements/export/pdf', 'Api\MouvementController::exportPdf');

    // État des stocks
    $routes->get('ecoulement/stock', 'Api\StockController::index');
    $routes->post('ecoulement/stock/export-pdf', 'Api\StockController::exportPdf');

    // Récapitulation des ventes
    $routes->get('recap/evolution', 'Api\RecapController::evolution');
    $routes->get('recap/clients', 'Api\RecapController::clients');
    $routes->get('recap/produits', 'Api\RecapController::produits');
    $routes->post('recap/export-pdf', 'Api\RecapController::exportPdf');
});