<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UserController.php';
require_once './controllers/ProductController.php';
require_once './controllers/TableController.php';
require_once './controllers/OrderController.php';

// Run php -S localhost:8080 -t app
// Instantiate App
$app = AppFactory::create();
// Add error middleware
$app->addErrorMiddleware(true, true, true);
// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->group('/users', function ($app) {
    $userController = new UserController();

    $app->post('/create', function (Request $request, Response $response) use ($userController) {
        $postData = $request->getParsedBody();
        $message = $userController->addUser($postData);
        $response->getBody()->write(json_encode(['response' => $message]));

        return $response;
    });

    $app->get('/getAll', function (Request $request, Response $response) use ($userController) {
        $allUsers = $userController->getUsers();
        $response->getBody()->write(json_encode(['response' => $allUsers]));

        return $response;
    });

    $app->get('/getById/{id}', function (Request $request, Response $response, $args) use ($userController) {
        $user = $userController->getUserById($args);
        $response->getBody()->write(json_encode(['response' => $user]));

        return $response;
    });

    $app->put('/disable/{id}', function (Request $request, Response $response, $args) use ($userController) {
        $userId = $args['id'];
        $data = $request->getParsedBody();
        $result = $userController->modifyUserStatus($data, $userId);


        $response->getBody()->write(json_encode(['response' => $result]));

        return $response;
    });

    $app->put('/update/{id}', function (Request $request, Response $response, $args) use ($userController) {
        $userId = $args['id'];
        $data = $request->getParsedBody();
        $result = $userController->updateUser($data, $userId);


        $response->getBody()->write(json_encode(['response' => $result]));

        return $response;
    });

});

$app->group('/products', function ($app) {
    $productController = new ProductController();

    $app->post('/create', function (Request $request, Response $response) use ($productController) {
        $postData = $request->getParsedBody();
        $message = $productController->addProduct($postData);
        $response->getBody()->write(json_encode(['response' => $message]));

        return $response;
    });

    $app->get('/getAll', function (Request $request, Response $response) use ($productController) {
        $allProducts = $productController->getProducts();
        $response->getBody()->write(json_encode(['response' => $allProducts]));

        return $response;
    });

    $app->get('/getById/{id}', function (Request $request, Response $response, $args) use ($productController) {
        $user = $productController->getProductById($args);
        $response->getBody()->write(json_encode(['response' => $user]));

        return $response;
    });

    $app->put('/disable/{id}', function (Request $request, Response $response, $args) use ($productController) {
        $productId = $args['id'];
        $data = $request->getParsedBody();
        $result = $productController->modifyProductStatus($data, $productId);


        $response->getBody()->write(json_encode(['response' => $result]));

        return $response;
    });

    $app->put('/update/{id}', function (Request $request, Response $response, $args) use ($productController) {
        $userId = $args['id'];
        $data = $request->getParsedBody();
        $result = $productController->updateProduct($data, $userId);


        $response->getBody()->write(json_encode(['response' => $result]));

        return $response;
    });

});

$app->group('/tables', function ($app) {
    $tableController = new TableController();

    $app->post('/create', function (Request $request, Response $response) use ($tableController) {
        $message = $tableController->addTable();
        $response->getBody()->write(json_encode(['response' => $message]));

        return $response;
    });

    $app->get('/getAll', function (Request $request, Response $response) use ($tableController) {
        $allTables = $tableController->getTables();
        $response->getBody()->write(json_encode(['response' => $allTables]));

        return $response;
    });

    $app->get('/getById/{id}', function (Request $request, Response $response, $args) use ($tableController) {
        $table = $tableController->getTableById($args);
        $response->getBody()->write(json_encode(['response' => $table]));

        return $response;
    });

    $app->put('/disable/{id}', function (Request $request, Response $response, $args) use ($tableController) {
        $tableId = $args['id'];
        $data = $request->getParsedBody();
        $result = $tableController->modifyTableStatus($data, $tableId);

        $response->getBody()->write(json_encode(['response' => $result]));

        return $response;
    });

    $app->put('/update/{id}', function (Request $request, Response $response, $args) use ($tableController) {
        $tableId = $args['id'];
        $data = $request->getParsedBody();
        $result = $tableController->updateTable($data, $tableId);


        $response->getBody()->write(json_encode(['response' => $result]));

        return $response;
    });
});

$app->group('/orders', function ($app) {
    $orderController = new OrderController();

    $app->post('/create', function (Request $request, Response $response) use ($orderController) {
        $data = $request->getParsedBody();
        $message = $orderController->addOrder($data);
        $response->getBody()->write(json_encode(['response' => $message]));

        return $response;
    });

    // $app->get('/getAll', function (Request $request, Response $response) use ($tableController) {
    //     $allTables = $tableController->getTables();
    //     $response->getBody()->write(json_encode(['response' => $allTables]));

    //     return $response;
    // });

    // $app->get('/getById/{id}', function (Request $request, Response $response, $args) use ($tableController) {
    //     $table = $tableController->getTableById($args);
    //     $response->getBody()->write(json_encode(['response' => $table]));

    //     return $response;
    // });

    // $app->put('/disable/{id}', function (Request $request, Response $response, $args) use ($tableController) {
    //     $tableId = $args['id'];
    //     $data = $request->getParsedBody();
    //     $result = $tableController->modifyTableStatus($data, $tableId);

    //     $response->getBody()->write(json_encode(['response' => $result]));

    //     return $response;
    // });

    // $app->put('/update/{id}', function (Request $request, Response $response, $args) use ($tableController) {
    //     $tableId = $args['id'];
    //     $data = $request->getParsedBody();
    //     $result = $tableController->updateTable($data, $tableId);


    //     $response->getBody()->write(json_encode(['response' => $result]));

    //     return $response;
    // });
});

$app->run();
