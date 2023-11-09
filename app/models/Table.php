<?php
require_once 'db/Data.php';
require_once 'models/BaseModel.php';

class Table extends BaseModel
{
    public $status;

    public function insertTable()
    {
        $dataObject = Data::getDataObject();

        $query = $dataObject->getQuery(
            "INSERT INTO tables (id, status, creation_date)
            VALUES ('$this->id', '$this->status', '$this->creationDate')"
        );
        return $query->execute();
    }

    public function updateTable()
    {
        $dataObject = Data::getDataObject();

        $query = $dataObject->getQuery(
            "UPDATE tables
            SET
                status = '$this->status',
                modification_date = '$this->modificationDate'
            WHERE id = '$this->id'"
        );
        return $query->execute();
    }

    public static function getAllTables()
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            id,
            status,
            creation_date as creationDate,
            modification_date as modificationDate,
            disabled
            FROM tables;"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'table');
    }

    public static function modifyDisabledStatus($tableId, $value)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "UPDATE tables
            SET disabled = $value
            WHERE id = '$tableId'"
        );
        return $query->execute();
    }

    public static function getTableById($id)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT
            id,
            status,
            creation_date as creationDate,
            modification_date as modificationDate,
            disabled
            FROM `tables` WHERE tables.id = '$id'"
        );

        $query->execute();
        return $query->fetchObject('table');
    }
}