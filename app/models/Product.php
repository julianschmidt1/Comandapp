<?php

require_once 'db/Data.php';
require_once 'models/BaseModel.php';

class Product extends BaseModel
{
    public $name;
    public $price;
    public $productType;
    public $delay;

    public function insertProduct()
    {
        $dataObject = Data::getDataObject();

        try {
            $query = $dataObject->getQuery(
                "INSERT INTO products (name, price, delay, product_type, creation_date)
            VALUES ('$this->name', '$this->price', '$this->delay', '$this->productType', '$this->creationDate')"
            );
            $query->execute();
            return $dataObject->getLastInsertedId();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function toCsv()
    {
        return [$this->id, $this->name, $this->price, $this->delay, $this->productType, $this->creationDate, $this->modificationDate, $this->disableDate, $this->disabled];
    }

    public function updateProduct()
    {
        $dataObject = Data::getDataObject();

        $query = $dataObject->getQuery(
            "UPDATE products
            SET
                name = '$this->name',
                price = $this->price,
                delay = $this->delay,
                product_type = $this->productType,
                modification_date = '$this->modificationDate'
            WHERE id = $this->id"
        );
        return $query->execute();
    }

    public static function modifyDisabledStatus($productId, $value, $disableDate)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "UPDATE products
            SET disabled = $value,
                disable_date = '$disableDate'
            WHERE id = $productId"
        );
        return $query->execute();
    }

    public static function getAllProducts()
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            id,
            name,
            price,
            product_type as productType,
            creation_date as creationDate,
            modification_date as modificationDate,
            disable_date as disableDate,
            delay,
            disabled
            FROM products;"
        );
        $query->execute();
        return $query->fetchAll(PDO::FETCH_CLASS, 'product');
    }

    public static function getProductById($productId)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT 
            id,
            name,
            price,
            product_type as productType,
            creation_date as creationDate,
            modification_date as modificationDate,
            delay,
            disabled
            FROM `products` WHERE products.id = $productId"
        );

        $query->execute();
        return $query->fetchObject('product');
    }
}

?>