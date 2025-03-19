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

    // Grupo de rotas para o Calendário
    $routes->group('calendario', function ($routes) {
        $routes->get('', 'FullCalendarController::index'); // Página do calendário
        $routes->get('data', 'FullCalendarController::getCalendarData'); // Dados do calendário
    });

    // Grupo de rotas para Agendamento
    $routes->group('agendamento', function ($routes) {
        $routes->get('novo', 'SchedulingController::add'); // Criar um novo agendamento
        $routes->post('salvar', 'SchedulingController::save'); // Salvar agendamento
        $routes->get('listar', 'SchedulingController::list'); // Listar agendamentos
        $routes->get('editar/(:num)', 'SchedulingController::edit/$1'); // Editar agendamento específico
        $routes->post('atualizar/(:num)', 'SchedulingController::update/$1'); // Atualizar agendamento
        $routes->post('deletar/(:num)', 'SchedulingController::delete/$1'); // Deletar agendamento
    });

    // Grupo de rotas para Recursos
    $routes->group('recursos', function ($routes) {
        $routes->post('getByEspacos', 'RecursosController::getByEspacos'); // Buscar recursos por espaços
        $routes->get('listar', 'RecursosController::list'); // Listar todos os recursos
        $routes->post('salvar', 'RecursosController::save'); // Adicionar um recurso
        $routes->get('editar/(:num)', 'RecursosController::edit/$1'); // Editar um recurso específico
        $routes->post('atualizar/(:num)', 'RecursosController::update/$1'); // Atualizar recurso
        $routes->post('deletar/(:num)', 'RecursosController::delete/$1'); // Excluir recurso
    });
});
