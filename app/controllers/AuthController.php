<?php

require_once 'utils/ResponseHelper.php';
require_once 'utils/JWTAuthenticator.php';
require_once 'models/User.php';

class AuthController
{
    public function Login($request, $response, $args)
    {
        $params = $request->getParsedBody();

        if (isset($params['mail'], $params['password'])) {
            $mail = $params['mail'];
            $password = $params['password'];
            $foundUser = User::validateUser($mail, $password);
            
            if ($foundUser instanceof User) {
                $token = JWTAuthenticator::GenerateToken(["id" => $foundUser->id, "roleId" => $foundUser->userTypeId]);
                return ResponseHelper::jsonResponse($response, ["response" => $token]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["response" => "Usuario o contraseña invalidos"]);
    }
}