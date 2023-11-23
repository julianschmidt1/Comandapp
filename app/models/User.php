<?php

require_once 'db/Data.php';
require_once 'models/BaseModel.php';

class User extends BaseModel
{
    public $name;
    public $userTypeId;
    public $mail;
    public $password;

    public function insertUser()
    {
        $dataObject = Data::getDataObject();

        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $query = $dataObject->getQuery(
            "INSERT INTO users (name, email, password, user_type_id, creation_date)
            VALUES ('$this->name', '$this->mail', '$hashedPassword', '$this->userTypeId', '$this->creationDate')"
        );
        $query->execute();
        return $dataObject->getLastInsertedId();
    }

    public function updateUser()
    {
        $dataObject = Data::getDataObject();

        $query = $dataObject->getQuery(
            "UPDATE users
            SET
                name = '$this->name',
                user_type_id = $this->userTypeId,
                modification_date = '$this->modificationDate'
            WHERE id = $this->id"
        );
        return $query->execute();
    }

    public static function getAllUsers()
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            id,
            name,
            user_type_id as userTypeId,
            creation_date as creationDate,
            modification_date as modificationDate,
            email as mail,
            password,
            disabled
            FROM users;"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'User');
    }

    public static function modifyDisabledStatus($userId, $value, $disableDate)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "UPDATE users
            SET disabled = $value,
                disable_date = '$disableDate'
            WHERE id = $userId"
        );
        return $query->execute();
    }

    public static function validateUser($email)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            id,
            name,
            password,
            user_type_id as userTypeId,
            creation_date as creationDate,
            modification_date as modificationDate,
            disabled
            FROM `users`
            WHERE users.email = '$email'
            AND users.disabled = 0"
        );

        $query->execute();
        return $query->fetchObject('User');
    }

    public static function getUserById($userId)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            id,
            name,
            user_type_id as userTypeId,
            creation_date as creationDate,
            modification_date as modificationDate,
            disabled
            FROM `users` WHERE users.id = $userId"
        );

        $query->execute();
        return $query->fetchObject('User');

    }
}

?>