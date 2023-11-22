<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

require_once 'models/Product.php';
require_once 'utils/ResponseHelper.php';

class ProductMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $args = $routeContext->getRoute()->getArguments();
        $product = Product::getProductById((int) $args['productId']);
        $roleId =$request->getAttribute('userType'); 

        if ($product instanceof Product) {

            if ($roleId === 5 || $roleId === $product->productType) {

                $response = new Response();
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