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

        $query = $dataObject->getQuery(
            "INSERT INTO orders (order_id, customer_name, product_id, creation_date)
            VALUES ('$id', '$customerName', $productId, '$creationDate')"
        );
        $query->execute();
        return $dataObject->getLastInsertedId();
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