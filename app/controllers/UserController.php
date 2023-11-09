<?php
require_once 'models/User.php';

class UserController
{

    public function addUser($data)
    {
        if (isset($data['name']) && isset($data['userTypeId'])) {
            $name = $data['name'];
            $userType = (int) $data['userTypeId'];
            if (strlen($name) > 3 && $userType >= 1 && $userType <= 5) {
                $newUser = new User();
                $newUser->name = $name;
                $newUser->userTypeId = $userType;
                $newUser->creationDate = date('Y-m-d H:i:s');
                return $newUser->insertUser();
            }
        }
        return "Uno de los parametros no es valido";
    }

    public function updateUser($data, $userId)
    {
        if (isset($data['user'])) {
            $userData = $data['user'];
            if (isset($userData['name']) && isset($userData['userTypeId'])) {
                $name = $userData['name'];
                $userType = (int) $userData['userTypeId'];
                if (strlen($name) > 3 && $userType >= 1 && $userType <= 5) {
                    $newUser = new User();
                    $newUser->id = $userId;
                    $newUser->name = $name;
                    $newUser->userTypeId = $userType;
                    $newUser->modificationDate = date('Y-m-d H:i:s');
                    return $newUser->updateUser() ? "Usuario modificado con exito" : "No se pudo modificar el usuario";
                }
            }
        }
        return "Uno de los parametros no es valido";
    }

    public function modifyUserStatus($data, $userId)
    {
        if (isset($data["value"])) {
            $disabledValue = (int) $data["value"];
            if (($disabledValue === 0 || $disabledValue === 1) && (int) $userId > 0) {
                return User::modifyDisabledStatus($userId, $disabledValue) ? "Usuario modificado con exito" : "Ocurrio un error al modificar el usuario";
            }
        }

        return "Uno de los parametros no es valido";
    }

    public function getUsers()
    {
        $allUsers = User::getAllUsers();

        return $allUsers;
    }

    public function getUserById($data)
    {
        if (isset($data['id'])) {
            $userId = (int) $data['id'];
            if ($userId > 0) {
                $user = User::getUserById((int) $data['id']);
                if ($user instanceof User) {
                    return $user;
                }
            }
        }

        return "El usuario no existe.";
    }

    public static function getUserData($user)
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'userTypeId' => $user->getUserTypeId(),
            'creationDate' => $user->getCreationDate(),
            'modificationDate' => $user->getModificationDate(),
            'disabled' => $user->getDisabled() === 1,
        ];
    }


}
