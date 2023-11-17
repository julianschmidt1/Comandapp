<?php
require_once 'models/Product.php';
require_once 'interfaces/IApiUsable.php';
require_once 'utils/ResponseHelper.php';

class ProductController implements IApiUsable
{

    public function Create($request, $response, $args)
    {
        $data = $request->getParsedBody();
        if (isset($data['name'], $data['price'], $data['productType'], $data['delay'])) {
            $productName = $data['name'];
            $productPrice = $data['price'];
            $productType = $data['productType'];
            $delay = $data['delay'];
            if (strlen($productName) > 3 && (float) $productPrice > 0 && (int) $productType >= 1 && (int) $productType <= 3) {
                $newProduct = new Product();
                $newProduct->name = $productName;
                $newProduct->price = (float) $productPrice;
                $newProduct->productType = (int) $productType;
                $newProduct->delay = (int) $delay;
                $newProduct->creationDate = date('Y-m-d H:i:s');

                $message = $newProduct->insertProduct();
                return ResponseHelper::jsonResponse($response, ["response" => $message]);
            }
        }
        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function Update($request, $response, $args)
    {
        $data = $request->getParsedBody();
        if (isset($data['product'])) {
            $productData = $data['product'];
            if (isset($productData['name'], $productData['productType'], $productData['price'], $productData['delay'])) {
                $name = $productData['name'];
                $productType = (int) $productData['productType'];
                $delay = (int) $productData['delay'];
                $price = (float) $productData['price'];
                if (strlen($name) > 3 && $productType >= 1 && $productType <= 3) {
                    $newProduct = new Product();
                    $newProduct->id = $args['id'];
                    $newProduct->name = $name;
                    $newProduct->price = $price;
                    $newProduct->productType = $productType;
                    $newProduct->delay = $delay;
                    $newProduct->modificationDate = date('Y-m-d H:i:s');
                    $message = $newProduct->updateProduct() ? "Producto modificado con exito" : "No se pudo modificar el producto";
                    return ResponseHelper::jsonResponse($response, ["response" => $message]);
                }
            }
        }
        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function GetAll($request, $response, $args)
    {
        $message = Product::getAllProducts();

        return ResponseHelper::jsonResponse($response, ["response" => $message]);
    }

    public function Delete($request, $response, $args)
    {
        $data = $request->getParsedBody();

        if (isset($data["value"])) {
            $disabledValue = (int) $data["value"];
            if (($disabledValue === 0 || $disabledValue === 1) && (int) $args['id'] > 0) {
                $message = Product::modifyDisabledStatus($args['id'], $disabledValue) ? "Producto modificado con exito" : "Ocurrio un error al modificar el producto";
                return ResponseHelper::jsonResponse($response, ["response" => $message]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function GetById($request, $response, $args)
    {
        if (isset($args['id'])) {
            $productId = (int) $args['id'];
            if ($productId > 0) {
                $product = Product::getProductById((int) $args['id']);
                if ($product instanceof Product) {
                    return ResponseHelper::jsonResponse($response, ["response" => $product]);
                }
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

}