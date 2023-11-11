<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UserController.php';
require_once './controllers/ProductController.php';
require_once './controllers/TableController.php';
require_once './controllers/OrderController.php';
require_once './middlewares/AuthMiddleware.php';

// Run php -S localhost:8080 -t app
// Instantiate App
$app = AppFactory::create();
// Add error middleware
$app->addErrorMiddleware(true, true, true);
// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->group('/users', function (RouteCollectorProxy $group) {
    $group->post('/create', \UserController::class . ':Create');
    $group->get('/getAll', \UserController::class . ':GetAll');
    $group->get('/getById/{id}', \UserController::class . ':GetById');
    $group->put('/disable/{id}', \UserController::class . ':Delete');
    $group->put('/update/{id}', \UserController::class . ':Update');
})->add(new AuthMiddleware());

$app->group('/products', function (RouteCollectorProxy $group) {
    $group->post('/create', \ProductController::class . ':Create');
    $group->get('/getAll', \ProductController::class . ':GetAll');
    $group->get('/getById/{id}', \ProductController::class . ':GetById');
    $group->put('/disable/{id}', \ProductController::class . ':Delete');
    $group->put('/update/{id}', \ProductController::class . ':Update');
})->add(new AuthMiddleware());

$app->group('/tables', function (RouteCollectorProxy $group) {
    $group->post('/create', \TableController::class . ':Create');
    $group->get('/getAll', \TableController::class . ':GetAll');
    $group->get('/getById/{id}', \TableController::class . ':GetById');
    $group->put('/disable/{id}', \TableController::class . ':Delete');
    $group->put('/update/{id}', \TableController::class . ':Update');
})->add(new AuthMiddleware());

$app->group('/orders', function (RouteCollectorProxy $group) {
    $group->post('/create', \OrderController::class . ':Create');
    $group->get('/getAll', \OrderController::class . ':GetAll');
    $group->get('/getById/{id}', \OrderController::class . ':GetById');
    $group->put('/update/{id}/{productId}', \OrderController::class . ':Update');
    $group->put('/disable/{id}/{productId}', \OrderController::class . ':Delete');
})->add(new AuthMiddleware());


$app->run();
