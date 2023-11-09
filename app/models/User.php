<?php

require_once 'db/Data.php';
require_once 'models/BaseModel.php';

class User extends BaseModel
{
    public $name;
    public $userTypeId;

    public function insertUser()
    {
        $dataObject = Data::getDataObject();

        $query = $dataObject->getQuery(
            "INSERT INTO users (name, user_type_id, creation_date)
            VALUES ('$this->name', '$this->userTypeId', '$this->creationDate')"
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
            disabled
            FROM users;"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'User');
    }

    public static function modifyDisabledStatus($userId, $value)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "UPDATE users
            SET disabled = $value
            WHERE id = $userId"
        );
        return $query->execute();
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