<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class TableUpdateMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $data = $request->getParsedBody();

        if (isset($data['orderId'], $data['tableId'], $data['tableRating'], $data['restaurantRating'], $data['waiterRating'], $data['chefRating'], $data['description'])) {
            return $handler->handle($request);
        }
        $response = new Response();
        return ResponseHelper::jsonResponse($response, ["response" => "Parametros invalidos"]);
    }
}