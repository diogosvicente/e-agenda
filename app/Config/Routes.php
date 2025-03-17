<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rotas públicas (Acesso livre)
$routes->group('', function ($routes) {
    $routes->get('login', 'LoginController::index', ['filter' => 'guest']);
    $routes->get('sobre', 'AboutController::index');
});

// Rotas protegidas (Requer autenticação)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    // Página inicial e autenticação
    $routes->get('/', 'HomeController::index');
    $routes->get('logout', 'LoginController::logout');

    // Calendário e eventos
    $routes->group('calendario', function ($routes) {
        $routes->get('', 'FullCalendarController::index'); // Página do calendário
        $routes->get('data', 'FullCalendarController::getCalendarData'); // Dados do calendário
    });

    // Agendamento
    $routes->get('agendamento/novo', 'SchedulingController::add');
});
