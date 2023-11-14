<?php
require_once 'db/Data.php';
require_once 'models/BaseModel.php';
class Order extends BaseModel
{
    public $customerName;
    public $status;
    public $estimatedDelay;
    public $productId;
    public $relatedTable;
    public $quantity;
    public $filePath;

    public function insertOrder()
    {
        $dataObject = Data::getDataObject();

        $query = $dataObject->getQuery(
            "INSERT INTO orders (order_id, customer_name, quantity, related_table, file_path, status, product_id, creation_date)
            VALUES ('$this->id', '$this->customerName', '$this->quantity', '$this->relatedTable', '$this->filePath', '$this->status', $this->productId, '$this->creationDate')"
        );
        $query->execute();
        return $dataObject->getLastInsertedId();
    }

    public function updateOrder()
    {
        $dataObject = Data::getDataObject();

        $query = $dataObject->getQuery(
            "UPDATE orders
            SET
                status = '$this->status',
                modification_date = '$this->modificationDate',
                estimated_delay = $this->estimatedDelay
            WHERE order_id = '$this->id'
            AND product_id = $this->productId"
        );
        return $query->execute();
    }

    public static function getOrderById($id)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            order_id as id,
            status,
            customer_name as customerName,
            estimated_delay as estimatedDelay,
            product_id as productId,
            creation_date as creationDate,
            modification_date as modificationDate,
            related_table as relatedTable,
            quantity,
            disabled
            FROM orders
            WHERE order_id = '$id';"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'order');
    }

    public static function getAllOrders()
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            order_id as id,
            status,
            customer_name as customerName,
            estimated_delay as estimatedDelay,
            product_id as productId,
            creation_date as creationDate,
            modification_date as modificationDate,
            related_table as relatedTable,
            quantity,
            disabled
            FROM orders;"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'order');
    }

    public static function getPending($userTypeId, $status)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
                order_id as id,
                status,
                customer_name as customerName,
                estimated_delay as estimatedDelay,
                product_id as productId,
                related_table as relatedTable,
                orders.creation_Date as creationDate,
                orders.disabled,
                quantity
            FROM orders
            LEFT JOIN products p
            ON p.id = orders.product_id
            WHERE p.product_type = $userTypeId
            AND status = '$status';"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'order');
    }

    public static function getOrderDelay($orderId, $tableId)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
                MAX(orders.estimated_delay) AS maxDelay
            FROM orders
            JOIN products p ON orders.product_id = p.id
            WHERE orders.related_table = '$tableId'
            AND orders.order_id = '$orderId';"
        );
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['maxDelay'];
    }

    public static function getFullBill($orderId, $tableId)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
                SUM(p.price * orders.quantity) AS totalPrice
            FROM orders
            JOIN products p ON orders.product_id = p.id
            WHERE orders.related_table = '$tableId'
            AND orders.order_id = '$orderId';"
        );
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['totalPrice'];
    }

    public static function getReady($status)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
                order_id as id,
                status,
                customer_name as customerName,
                estimated_delay as estimatedDelay,
                product_id as productId,
                related_table as relatedTable,
                orders.creation_Date as creationDate,
                orders.disabled,
                quantity
            FROM orders
            LEFT JOIN products p
            ON p.id = orders.product_id
            WHERE status = '$status';"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'order');
    }

    public static function modifyDisabledStatus($orderId, $productId, $value)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "UPDATE orders
            SET disabled = $value
            WHERE order_id = '$orderId'
            AND product_id = $productId"
        );
        return $query->execute();
    }

}