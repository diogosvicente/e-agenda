<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('/sobre', 'SobreController::index');
$routes->get('login', 'LoginController::index');
$routes->get('logout', 'LoginController::logout');
$routes->get('callback', 'SSOController::callback');

// 🔒 Rotas protegidas (exemplo)
$routes->group('', ['filter' => 'jwtMiddleware'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
});
