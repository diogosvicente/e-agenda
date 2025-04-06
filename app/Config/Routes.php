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
    // Página de login/autenticação
    $routes->get('logout', 'LoginController::logout');

    // Grupo de rotas para o Calendário
    $routes->group('/', function ($routes) {
        $routes->get('', 'FullCalendarController::index'); // Página do calendário
        $routes->get('calendario/data', 'FullCalendarController::getCalendarData'); // Dados do calendário
    });

    // Grupo de rotas para Agendamento
    $routes->group('agendamento', function ($routes) {
        $routes->get('novo', 'SchedulingController::add'); // Criar um novo agendamento
        $routes->post('salvar', 'SchedulingController::save'); // Salvar agendamento
        $routes->get('listar', 'SchedulingController::list'); // Listar agendamentos
        $routes->get('editar/(:num)', 'SchedulingController::edit/$1'); // Editar agendamento específico
        $routes->post('atualizar/(:num)', 'SchedulingController::update/$1'); // Atualizar agendamento
        $routes->post('deletar/(:num)', 'SchedulingController::delete/$1'); // Deletar agendamento
        $routes->get('aprovar/(:segment)', 'SchedulingController::approve/$1');
        $routes->post('confirmar_aprovacao', 'SchedulingController::confirm_approval');
        //http://152.92.228.130/e-agenda/aprovar_evento/c36fb6780dc8208907f5dc3580dbef7d
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

    $routes->group('pdf', function ($routes) {
        $routes->get('gerar/(:segment)', 'MakePDFController::generatePDF/$1');
    });
});
