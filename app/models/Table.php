<?php
require_once 'db/Data.php';
require_once 'models/BaseModel.php';

class Table extends BaseModel
{
    private $_id;
    private $_status;

    public function insertTable()
    {
        $dataObject = Data::getDataObject();

        $id = $this->getId();
        $status = $this->getStatus();
        $creationDate = $this->getCreationDate();

        $query = $dataObject->getQuery(
            "INSERT INTO tables (id, status, creation_date)
            VALUES ('$id', '$status', '$creationDate')"
        );
        return $query->execute();
    }

    public function updateTable()
    {
        $dataObject = Data::getDataObject();

        $userId = $this->getId();
        $status = $this->getStatus();
        $modificationDate = $this->getModificationDate();

        $query = $dataObject->getQuery(
            "UPDATE tables
            SET
                status = '$status',
                modification_date = '$modificationDate'
            WHERE id = '$userId'"
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
            creation_date,
            modification_date,
            disabled
            FROM tables;"
        );
        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);
        $tables = [];

        if (!empty($queryResult)) {
            foreach ($queryResult as $result) {
                $table = Table::convertAssocToTableObject($result);
                $tables[] = $table;
            }
        }

        return $tables;
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
            "SELECT * FROM `tables` WHERE tables.id = '$id'"
        );

        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($queryResult)) {
            $table = Table::convertAssocToTableObject($queryResult[0]);
            return $table;
        }
        return null;
    }

    public static function convertAssocToTableObject($assocData)
    {
        $table = new Table();

        $table->setId($assocData['id']);
        $table->setStatus($assocData['status']);
        $table->setCreationDate($assocData['creation_date']);
        $table->setModificationDate($assocData['modification_date']);
        $table->setDisabled($assocData['disabled']);

        return $table;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }
}