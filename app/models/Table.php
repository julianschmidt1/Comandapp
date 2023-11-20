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

    public static function insertTableReview($tableId, $orderId, $tableRating, $restaurantRating, $waiterRating, $chefRating, $description, $creationDate)
    {
        $dataObject = Data::getDataObject();

        $query = $dataObject->getQuery(
            "INSERT INTO reviews (order_id, related_table, table_rating, restaurant_rating, waiter_rating, chef_rating, description, creation_date)
            VALUES ('$tableId', '$orderId', '$tableRating', '$restaurantRating', '$waiterRating', '$chefRating', '$description', '$creationDate')"
        );
        return $query->execute();
    }

    public static function getBestReviews()
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT *,
            (table_rating + restaurant_rating + waiter_rating + chef_rating) AS total_rating
            FROM reviews
            ORDER BY total_rating DESC
            LIMIT 5;"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMostUsed()
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT related_table as tableId,
            COUNT(DISTINCT order_id) AS usageCounter
            FROM orders
            GROUP BY related_table
            ORDER BY usageCounter DESC
            LIMIT 1;"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
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