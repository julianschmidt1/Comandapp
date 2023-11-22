<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once 'models/Product.php';
require_once 'utils/ResponseHelper.php';

class UserRoleMiddleware
{
    private $_allowedRoles;
    public function __construct($allowedRoles = [])
    {
        if (!in_array(5, $allowedRoles)) {
            $allowedRoles[] = 5; // idRole admin
        }

        $this->_allowedRoles = $allowedRoles;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (in_array($request->getAttribute('userType'), $this->_allowedRoles)) {
            return $handler->handle($request);
        } else {
            $response = new Response();
            return ResponseHelper::jsonResponse($response, ["error" => "No posees permisos para acceder a los datos"]);
        }
    }
}