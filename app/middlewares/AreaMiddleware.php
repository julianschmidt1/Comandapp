<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

require_once 'models/Product.php';
require_once 'utils/ResponseHelper.php';

class AreaMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $args = $routeContext->getRoute()->getArguments();
        $userType = $request->getAttribute('userType');
        $product = Product::getProductById((int) $args['productId']);

        if ($product instanceof Product) {

            if ($userType === 5 || $userType === $product->productType) {

                return $handler->handle($request);
            } else {
                $response = new Response();
                return ResponseHelper::jsonResponse($response, ["error" => "El usuario ingresado no tiene permisos para acceder a ese tipo de producto"]);
            }
        } else {
            $response = new Response();
            return ResponseHelper::jsonResponse($response, ["response" => "El producto ingresado no existe"]);
        }
    }
}