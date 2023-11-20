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

// controllers
require_once './controllers/UserController.php';
require_once './controllers/ProductController.php';
require_once './controllers/TableController.php';
require_once './controllers/OrderController.php';
require_once './controllers/AuthController.php';
// middlewares
require_once './middlewares/AuthMiddleware.php';
require_once './middlewares/UserRoleMiddleware.php';
require_once './middlewares/ProductMiddleware.php';

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
})
    ->add(new UserRoleMiddleware())
    ->add(new AuthMiddleware());

$app->group('/products', function (RouteCollectorProxy $group) {
    $group->post('/create', \ProductController::class . ':Create')
        ->add(new UserRoleMiddleware())
        ->add(new AuthMiddleware());

    $group->get('/export', \ProductController::class . ':Export')
        ->add(new UserRoleMiddleware())
        ->add(new AuthMiddleware());

    $group->post('/import', \ProductController::class . ':Import')
        ->add(new UserRoleMiddleware())
        ->add(new AuthMiddleware());

    $group->get('/getAll', \ProductController::class . ':GetAll');
    $group->get('/getById/{id}', \ProductController::class . ':GetById');
    $group->put('/disable/{id}', \ProductController::class . ':Delete')
        ->add(new UserRoleMiddleware())
        ->add(new AuthMiddleware());
    $group->put('/update/{id}', \ProductController::class . ':Update')
        ->add(new UserRoleMiddleware())
        ->add(new AuthMiddleware());
});

$app->group('/tables', function (RouteCollectorProxy $group) {

    $group->post('/create', \TableController::class . ':Create')
        ->add(new UserRoleMiddleware());

    $group->get('/getAll', \TableController::class . ':GetAll')
        ->add(new UserRoleMiddleware([4]));

    $group->get('/getById/{id}', \TableController::class . ':GetById')
        ->add(new UserRoleMiddleware([4]));

    $group->put('/disable/{id}', \TableController::class . ':Delete')
        ->add(new UserRoleMiddleware());

    $group->put('/update/{id}', \TableController::class . ':Update')
        ->add(new UserRoleMiddleware([4]));
})->add(new AuthMiddleware());

$app->group('/orders', function (RouteCollectorProxy $group) {
    $group->post('/create', \OrderController::class . ':Create')->add(new UserRoleMiddleware([4]));
    $group->get('/getAll', \OrderController::class . ':GetAll')->add(new UserRoleMiddleware([4]));
    $group->get('/getPending', \OrderController::class . ':GetPending')->add(new UserRoleMiddleware([1, 2, 3, 4]));
    $group->get('/getReady', \OrderController::class . ':GetReady')->add(new UserRoleMiddleware([4]));
    $group->get('/getBill/{orderId}/{tableId}', \OrderController::class . ':GetBill')->add(new UserRoleMiddleware([4]));
    $group->get('/getDelay/{orderId}/{tableId}', \OrderController::class . ':GetOrderDelay');
    $group->get('/getById/{id}', \OrderController::class . ':GetById');
    $group->put('/update/{id}/{productId}', \OrderController::class . ':Update')
        ->add(new ProductMiddleware())
        ->add(new UserRoleMiddleware([1, 2, 3]));
    $group->put('/disable/{id}/{productId}', \OrderController::class . ':Delete');
})->add(new AuthMiddleware());

$app->group('/reviews', function (RouteCollectorProxy $group) {
    $group->post('/create', \TableController::class . ':CreateReview');
    $group->get('/getFeatured', \TableController::class . ':GetBestReviews')
        ->add(new UserRoleMiddleware())
        ->add(new AuthMiddleware());

    $group->get('/getMostUsed', \TableController::class . ':GetMostUsed')
        ->add(new UserRoleMiddleware())
        ->add(new AuthMiddleware());
});

$app->group('/auth', function (RouteCollectorProxy $group) {
    $group->post('/login', \AuthController::class . ':Login');
});

$app->run();
