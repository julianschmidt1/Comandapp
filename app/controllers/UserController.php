<?php
require_once 'models/User.php';
require_once 'interfaces/IApiUsable.php';
require_once 'utils/ResponseHelper.php';

class UserController implements IApiUsable
{

    public function Create($request, $response, $args)
    {
        $data = $request->getParsedBody();
        if (isset($data['name'], $data['userTypeId'])) {
            $name = $data['name'];
            $userType = (int) $data['userTypeId'];
            if (strlen($name) > 3 && $userType >= 1 && $userType <= 5) {
                $newUser = new User();
                $newUser->name = $name;
                $newUser->userTypeId = $userType;
                $newUser->creationDate = date('Y-m-d H:i:s');

                $message = $newUser->insertUser() ? "Usuario dado de alta con exito" : "Ocurrio un error en el alta de usuario";
                return ResponseHelper::jsonResponse($response, ["response" => $message]);
            } else {
                return ResponseHelper::jsonResponse($response, ["error" => "Parametros invalidos"]);
            }
        } else {
            return ResponseHelper::jsonResponse($response, ["error" => "Parametros faltantes"]);
        }
    }

    public function Update($request, $response, $args)
    {
        $data = $request->getParsedBody();
        if (isset($data['user'])) {
            $userData = $data['user'];
            if (isset($userData['name']) && isset($userData['userTypeId'])) {
                $name = $userData['name'];
                $userType = (int) $userData['userTypeId'];
                if (strlen($name) > 3 && $userType >= 1 && $userType <= 5) {
                    $newUser = new User();
                    $newUser->id = $args['id'];
                    $newUser->name = $name;
                    $newUser->userTypeId = $userType;
                    $newUser->modificationDate = date('Y-m-d H:i:s');

                    $message = $newUser->updateUser() ? "Usuario modificado con exito" : "No se pudo modificar el usuario";
                    return ResponseHelper::jsonResponse($response, ["response" => $message]);
                }
            }
        }
        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function Delete($request, $response, $args)
    {
        $data = $request->getParsedBody();
        if (isset($data["value"])) {
            $disabledValue = (int) $data["value"];
            if (($disabledValue === 0 || $disabledValue === 1) && (int) $args['id'] > 0) {
                $message = User::modifyDisabledStatus($args['id'], $disabledValue) ? "Usuario modificado con exito" : "Ocurrio un error al modificar el usuario";
                return ResponseHelper::jsonResponse($response, ["response" => $message]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function GetAll($request, $response, $args)
    {
        $allUsers = User::getAllUsers();

        return ResponseHelper::jsonResponse($response, ["response" => $allUsers]);
    }

    public function GetById($request, $response, $args)
    {
        if (isset($args['id'])) {
            $userId = (int) $args['id'];
            if ($userId > 0) {
                $user = User::getUserById((int) $args['id']);
                if ($user instanceof User) {
                    return ResponseHelper::jsonResponse($response, ["response" => $user]);

                }
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "El usuario no existe."]);
    }

}
