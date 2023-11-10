<?php
require_once 'models/Product.php';
require_once 'interfaces/IApiUsable.php';
require_once 'utils/ResponseHelper.php';

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
                $newProduct->name = $productName;
                $newProduct->price = (float) $productPrice;
                $newProduct->productType = (int) $productType;
                $newProduct->creationDate = date('Y-m-d H:i:s');

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
                    $newProduct->id = $userId;
                    $newProduct->name = $name;
                    $newProduct->price = $price;
                    $newProduct->productType = $productType;
                    $newProduct->modificationDate = date('Y-m-d H:i:s');
                    return $newProduct->updateProduct() ? "Producto modificado con exito" : "No se pudo modificar el producto";
                }
            }
        }
        return "Uno de los parametros no es valido";
    }

    public function getProducts()
    {
        return Product::getAllProducts();
    }

    public function modifyProductStatus($data, $productId)
    {
        if (isset($data["value"])) {
            $disabledValue = (int) $data["value"];
            if (($disabledValue === 0 || $disabledValue === 1) && (int) $productId > 0) {
                return Product::modifyDisabledStatus($productId, $disabledValue) ? "Producto modificado con exito" : "Ocurrio un error al modificar el producto";
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
                if ($product instanceof Product) {
                    return $product;
                }
            }
        }

        return "El usuario no existe.";
    }

}