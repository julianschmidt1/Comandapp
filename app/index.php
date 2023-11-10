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

// Implementar baja logica
$app->group('/orders', function ($app) {
    $orderController = new OrderController();

    $app->post('/create', function (Request $request, Response $response) use ($orderController) {
        $data = $request->getParsedBody();
        $message = $orderController->addOrder($data);
        $response->getBody()->write(json_encode(['response' => $message]));

        return $response;
    });

    $app->get('/getAll', function (Request $request, Response $response) use ($orderController) {
        $allOrders = $orderController->getOrders();
        $response->getBody()->write(json_encode(['response' => $allOrders]));

        return $response;
    });

    $app->put('/update/{id}/{productId}', function (Request $request, Response $response, $args) use ($orderController) {
        $orderId = $args['id'];
        $productId = $args['productId'];
        $data = $request->getParsedBody();
        $result = $orderController->updateOrder($data, $orderId, $productId);

        $response->getBody()->write(json_encode(['response' => $result]));

        return $response;
    });

    $app->get('/getById/{id}', function (Request $request, Response $response, $args) use ($orderController) {
        $order = $orderController->getOrderById($args);
        $response->getBody()->write(json_encode(['response' => $order]));

        return $response;
    });

});

$app->run();
