<?php
require_once 'db/Data.php';
require_once 'models/BaseModel.php';
class Order extends BaseModel
{
    private $_customerName;
    private $_status;
    private $_estimatedDelay;
    private $_productId;

    public function insertOrder()
    {
        $dataObject = Data::getDataObject();

        $id = $this->getId();
        $customerName = $this->getCustomerName();
        $productId = $this->getProductId();
        $creationDate = $this->getCreationDate();
        $status = $this->getStatus();

        $query = $dataObject->getQuery(
            "INSERT INTO orders (order_id, customer_name, status, product_id, creation_date)
            VALUES ('$id', '$customerName', '$status', $productId, '$creationDate')"
        );
        $query->execute();
        return $dataObject->getLastInsertedId();
    }

    public function updateOrder()
    {
        $dataObject = Data::getDataObject();

        $orderId = $this->getId();
        $status = $this->getStatus();
        $delay = $this->getEstimatedDelay();
        $productId = $this->getProductId();
        $modificationDate = $this->getModificationDate();

        $query = $dataObject->getQuery(
            "UPDATE orders
            SET
                status = '$status',
                modification_date = '$modificationDate',
                estimated_delay = $delay
            WHERE order_id = '$orderId'
            AND product_id = $productId"
        );
        return $query->execute();
    }

    public static function getOrderById($id)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            order_id,
            status,
            customer_name,
            estimated_delay,
            product_id,
            creation_date,
            modification_date,
            disabled
            FROM orders
            WHERE order_id = '$id';"
        );
        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);
        $orders = [];

        if (!empty($queryResult)) {
            foreach ($queryResult as $result) {
                $order = Order::convertAssocToOrderObject($result);
                $orders[] = $order;
            }
        }

        return $orders;
    }

    public static function getAllOrders()
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            order_id,
            status,
            customer_name,
            estimated_delay,
            product_id,
            creation_date,
            modification_date,
            disabled
            FROM orders;"
        );
        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);
        $orders = [];

        if (!empty($queryResult)) {
            foreach ($queryResult as $result) {
                $order = Order::convertAssocToOrderObject($result);
                $orders[] = $order;
            }
        }

        return $orders;
    }

    public static function convertAssocToOrderObject($assocData)
    {
        $order = new Order();

        $order->setId($assocData['order_id']);
        $order->setStatus($assocData['status']);
        $order->setCustomerName($assocData['customer_name']);
        $order->setEstimatedDelay($assocData['estimated_delay']);
        $order->setProductId($assocData['product_id']);
        $order->setCreationDate($assocData['creation_date']);
        $order->setModificationDate($assocData['modification_date']);

        return $order;
    }

    public function setCustomerName($customerName)
    {
        $this->_customerName = $customerName;
    }

    public function getCustomerName()
    {
        return $this->_customerName;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setEstimatedDelay($estimatedDelay)
    {
        $this->_estimatedDelay = $estimatedDelay;
    }

    public function getEstimatedDelay()
    {
        return $this->_estimatedDelay;
    }

    public function setProductId($productId)
    {
        $this->_productId = $productId;
    }

    public function getProductId()
    {
        return $this->_productId;
    }

}