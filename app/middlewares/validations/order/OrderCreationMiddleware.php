<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class OrderCreationMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $orderData = $request->getParsedBody();
        $imageData = $request->getUploadedFiles();

        if (isset($orderData['customerName'], $orderData['products'], $orderData['relatedTable'], $imageData['image'])) {
            return $handler->handle($request);
        }
        $response = new Response();
        return ResponseHelper::jsonResponse($response, ["response" => "Parametros invalidos"]);
    }
}