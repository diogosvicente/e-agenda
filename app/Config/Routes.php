<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'HomeController::index');

$routes->group('', ['filter' => 'jwtAuth'], function ($routes) {
    $routes->get('/', 'HomeController::index');
    $routes->get('sobre', 'SobreController::index');
    $routes->get('callback', 'SSOController::callback');
    // Outras rotas que exigem token
});
