<?php

require_once 'db/Data.php';
require_once 'models/BaseModel.php';

class User extends BaseModel
{
    private $_name;
    private $_userTypeId;

    public function insertUser()
    {
        $dataObject = Data::getDataObject();

        $name = $this->getName();
        $userTypeId = $this->getUserTypeId();
        $creationDate = $this->getCreationDate();

        $query = $dataObject->getQuery(
            "INSERT INTO users (name, user_type_id, creation_date)
            VALUES ('$name', '$userTypeId', '$creationDate')"
        );
        $query->execute();
        return $dataObject->getLastInsertedId();
    }

    public function updateUser()
    {
        $dataObject = Data::getDataObject();

        $userId = $this->getId();
        $name = $this->getName();
        $userTypeId = $this->getUserTypeId();
        $modificationDate = $this->getModificationDate();

        $query = $dataObject->getQuery(
            "UPDATE users
            SET
                name = '$name',
                user_type_id = $userTypeId,
                modification_date = '$modificationDate'
            WHERE id = $userId"
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
            user_type_id,
            creation_date,
            modification_date,
            disabled
            FROM users;"
        );
        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);
        $users = [];

        if (!empty($queryResult)) {
            foreach ($queryResult as $result) {
                $user = User::convertAssocToUserObject($result);
                $users[] = $user;
            }
        }

        return $users;
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
            "SELECT * FROM `users` WHERE users.id = $userId"
        );

        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($queryResult)) {
            $user = User::convertAssocToUserObject($queryResult[0]);
            return $user;
        }
        return null;
    }

    public static function convertAssocToUserObject($assocData)
    {
        $user = new User();

        $user->setId($assocData['id']);
        $user->setName($assocData['name']);
        $user->setUserTypeId($assocData['user_type_id']);
        $user->setCreationDate($assocData['creation_date']);
        $user->setModificationDate($assocData['modification_date']);
        $user->setDisabled($assocData['disabled']);

        return $user;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function setUserTypeId($userTypeId)
    {
        $this->_userTypeId = $userTypeId;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getUserTypeId()
    {
        return $this->_userTypeId;
    }

}

?>