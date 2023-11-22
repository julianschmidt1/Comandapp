<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once 'models/User.php';
require_once 'utils/ResponseHelper.php';
require_once 'utils/JWTAuthenticator.php';

class AuthMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            JWTAuthenticator::CheckToken($token);
            $tokenData = JWTAuthenticator::GetData($token);
            $request = $request->withAttribute('userType', $tokenData->roleId);

            return $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            return ResponseHelper::jsonResponse($response, ["response" => "ERROR: Hubo un error con el TOKEN"]);
        }
    }

    public static function checkToken(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            JWTAuthenticator::CheckToken($token);
            return $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            return ResponseHelper::jsonResponse($response, ["response" => "ERROR: Hubo un error con el TOKEN"]);
        }
    }
}