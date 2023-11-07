<?php
require_once 'models/Product.php';

class ProductController
{

    public function addProduct($data)
    {
        if (isset($data['name']) && isset($data['price']) && isset($data['productType'])) {
            $productName = $data['name'];
            $productPrice = $data['price'];
            $productType = $data['productType'];
            if (strlen($productName) > 3 && (float) $productPrice > 0 && (int) $productType >= 1 && (int) $productType <= 3) {
                $newProduct = new Product();
                $newProduct->setName($productName);
                $newProduct->setPrice((float) $productPrice);
                $newProduct->setProductType((int) $productType);
                $newProduct->setCreationDate(date('Y-m-d H:i:s'));

                return $newProduct->insertProduct();
            }
        }
        return "Uno de los parametros no es valido";
    }

    public function updateProduct($data, $userId)
    {
        if (isset($data['product'])) {
            $productData = $data['product'];
            if (isset($productData['name']) && isset($productData['productType']) && isset($productData['price'])) {
                $name = $productData['name'];
                $productType = (int) $productData['productType'];
                $price = (float) $productData['price'];
                if (strlen($name) > 3 && $productType >= 1 && $productType <= 3) {
                    $newProduct = new Product();
                    $newProduct->setId($userId);
                    $newProduct->setName($name);
                    $newProduct->setPrice($price);
                    $newProduct->setProductType($productType);
                    $newProduct->setModificationDate(date('Y-m-d H:i:s'));
                    return $newProduct->updateProduct() ? "Producto modificado con exito" : "No se pudo modificar el producto";
                }
            }
        }
        return "Uno de los parametros no es valido";
    }

    public function getProducts()
    {

        $allProducts = Product::getAllProducts();

        if (count($allProducts) > 0) {
            $productsData = array_map(function ($product) {
                return ProductController::getProductData($product);
            }, $allProducts);
        }

        return $productsData;
    }

    public function modifyProductStatus($data, $productId)
    {
        if (isset($data["value"])) {
            $disabledValue = (int) $data["value"];
            if (($disabledValue === 0 || $disabledValue === 1) && (int) $productId > 0) {
                $userStatus = Product::modifyDisabledStatus($productId, $disabledValue);

                return $userStatus ? "Producto modificado con exito" : "Ocurrio un error al modificar el producto";
            }
        }

        return "Uno de los parametros no es valido";
    }

    public function getProductById($data)
    {
        if (isset($data['id'])) {
            $productId = (int) $data['id'];
            if ($productId > 0) {
                $product = Product::getProductById((int) $data['id']);
                if ($product !== null) {
                    return ProductController::getProductData($product);
                }
            }
        }

        return "El usuario no existe.";
    }

    public static function getProductData($product)
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'productType' => $product->getProductType(),
            'price' => $product->getPrice(),
            'creationDate' => $product->getCreationDate(),
            'modificationDate' => $product->getModificationDate(),
            'disabled' => $product->getDisabled() === 1,
        ];
    }
}