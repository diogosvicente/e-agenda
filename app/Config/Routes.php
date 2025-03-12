<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('login', 'LoginController::index', ['filter' => 'guest']);
$routes->get('sobre', 'SobreController::index');

$routes->group('', ['filter' => 'auth'], function ($routes) {
     $routes->get('/', 'HomeController::index');
     $routes->get('logout', 'LoginController::logout');
     $routes->get('calendario', 'CalendarController::index');
});
