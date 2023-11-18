<?php

require_once 'utils/ResponseHelper.php';
require_once 'utils/JWTAuthenticator.php';
require_once 'models/User.php';

class AuthController
{
    public function Login($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if (isset($params['mail'], $params['passowrd'])) {
            $mail = $params['mail'];
            $password = $params['password'];

            if (User::validateUser($mail, $password) instanceof User) {
                $token = JWTAuthenticator::GenerateToken(["mail" => $mail]);
                return ResponseHelper::jsonResponse($response, ["response" => $token]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["response" => "Usuario o contraseña invalidos"]);
    }
}