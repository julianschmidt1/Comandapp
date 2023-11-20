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

    public function Export($request, $response)
    {
        $products = Product::getCsvProducts();

        $csvFileName = "products_" . date('Y-m-d-H-i-s') . ".csv";
        $fullPath = "../../CsvExportados/" . $csvFileName;

        if (count($products) > 0) {
            $csvFile = fopen($fullPath, 'w');
            fputcsv($csvFile, array_keys($products[0]));

            foreach ($products as $row) {
                fputcsv($csvFile, $row);
            }
            fclose($csvFile);

            return ResponseHelper::jsonResponse($response, ["reponse" => "Archivo generado con exito"]);
        } else {
            return ResponseHelper::jsonResponse($response, ["error" => "El archivo esta vacio"]);
        }
    }

    public function Import($request, $response)
    {
        $params = $request->getQueryParams();

        if (isset($params['fileName'])) {

            $fileName = $params['fileName'] . ".csv";
            $result = Product::insertCsvProduct("../../CsvExportados/" . $fileName, "products");

            if ($result) {
                return ResponseHelper::jsonResponse($response, ["reponse" => "Archivo importado con exito"]);
            } else {
                return ResponseHelper::jsonResponse($response, ["error" => "Ocurrio un error al importar el archivo"]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Parametros faltantes"]);
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