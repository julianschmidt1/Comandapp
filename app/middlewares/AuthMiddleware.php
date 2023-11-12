<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once 'models/User.php';
require_once 'utils/ResponseHelper.php';

class AuthMiddleware
{
    private $_allowedRoles;

    public function __construct($allowedRoles = [])
    {
        if (!in_array(5, $allowedRoles)) {
            $allowedRoles[] = 5;
        }

        $this->_allowedRoles = $allowedRoles;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $params = $request->getQueryParams();

        if (isset($params['mail'], $params['password'])) {

            $user = User::validateUser($params['mail'], $params['password']);

            if ($user instanceof User) {

                $userType = $user->userTypeId;
                if (in_array($userType, $this->_allowedRoles)) {
                    $request = $request->withAttribute('userType', $userType);
                    return $handler->handle($request);
                } else {
                    $response = new Response();
                    return ResponseHelper::jsonResponse($response, ["response" => "Usuario invalido"]);
                }
            } else {
                $response = new Response();

                return ResponseHelper::jsonResponse($response, ["response" => "El usuario de ingreso no existe"]);
            }
        } else {
            $response = new Response();
            return ResponseHelper::jsonResponse($response, ["response" => "Parametros invalidos"]);
        }
    }
}