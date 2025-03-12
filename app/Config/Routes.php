<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 $routes->get('login', 'LoginController::index', ['filter' => 'guest']); // Apenas usuários deslogados podem acessar

 $routes->group('', ['filter' => 'auth'], function ($routes) {
     $routes->get('/', 'HomeController::index'); // Página inicial protegida
     $routes->get('sobre', 'SobreController::index'); // Página sobre protegida
     $routes->get('logout', 'LoginController::logout'); // Logout
 });
 