<?php

require_once 'db/Data.php';
require_once 'models/BaseModel.php';

class Product extends BaseModel
{
    private $_name;
    private $_price;
    private $_productType;

    public function insertProduct()
    {
        $dataObject = Data::getDataObject();

        $name = $this->getName();
        $price = $this->getPrice();
        $productType = $this->getProductType();
        $creationDate = $this->getCreationDate();

        $query = $dataObject->getQuery(
            "INSERT INTO products (name, price, product_type, creation_date)
            VALUES ('$name', '$price', '$productType', '$creationDate')"
        );
        $query->execute();
        return $dataObject->getLastInsertedId();
    }

    public function updateProduct()
    {
        $dataObject = Data::getDataObject();

        $productId = $this->getId();
        $name = $this->getName();
        $price = $this->getPrice();
        $productType = $this->getProductType();
        $modificationDate = $this->getModificationDate();

        $query = $dataObject->getQuery(
            "UPDATE products
            SET
                name = '$name',
                price = $price,
                product_type = $productType,
                modification_date = '$modificationDate'
            WHERE id = $productId"
        );
        return $query->execute();
    }

    public static function modifyDisabledStatus($productId, $value)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "UPDATE products
            SET disabled = $value
            WHERE id = $productId"
        );
        return $query->execute();
    }

    public static function getAllProducts()
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT id, name, price, product_type, creation_date, modification_date, disabled FROM products;"
        );
        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);
        $products = [];

        if (!empty($queryResult)) {
            foreach ($queryResult as $result) {
                $product = Product::convertAssocToProductObject($result);
                $products[] = $product;
            }
        }

        return $products;
    }

    public static function getProductById($productId)
    {
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "SELECT * FROM `products` WHERE products.id = $productId"
        );

        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($queryResult)) {
            $product = Product::convertAssocToProductObject($queryResult[0]);
            return $product;
        }
        return null;
    }

    public static function convertAssocToProductObject($assocData)
    {
        $product = new Product();

        $product->setId($assocData['id']);
        $product->setName($assocData['name']);
        $product->setPrice($assocData['price']);
        $product->setProductType($assocData['product_type']);
        $product->setCreationDate($assocData['creation_date']);
        $product->setModificationDate($assocData['modification_date']);
        $product->setDisabled($assocData['disabled']);

        return $product;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getPrice()
    {
        return $this->_price;
    }

    public function getProductType()
    {
        return $this->_productType;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function setPrice($price)
    {
        $this->_price = $price;
    }

    public function setProductType($productType)
    {
        $this->_productType = $productType;
    }
}

?>