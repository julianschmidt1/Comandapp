<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class DisableMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $data = $request->getParsedBody();

        if (isset($data["value"])) {
            return $handler->handle($request);
        }
        $response = new Response();
        return ResponseHelper::jsonResponse($response, ["response" => "Parametros invalidos"]);
    }
}