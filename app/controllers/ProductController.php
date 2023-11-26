<?php
require_once 'models/Product.php';
require_once 'interfaces/IApiUsable.php';
require_once 'utils/ResponseHelper.php';

class ProductController implements IApiUsable
{

    public function Create($request, $response, $args)
    {
        $data = $request->getParsedBody();

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
        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function Update($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $productData = $data['product'];
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
        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function Export($request, $response)
    {
        $productList = Product::getAllProducts();

        $csvFileName = "products_" . date('Y-m-d-H-i-s') . ".csv";

        if (count($productList) > 0) {
            $response = $response->withHeader('Content-Type', 'text/csv');
            $response = $response->withHeader('Content-Disposition', 'attachment; filename=' . $csvFileName);
            $file = fopen('php://output', 'w');

            foreach ($productList as $product) {
                fputcsv($file, $product->toCsv());
            }
            fclose($file);

            return $response;
        } else {
            return ResponseHelper::jsonResponse($response, ["error" => "El archivo está vacío"]);
        }
    }


    public function Import($request, $response)
    {

        $uploadedFile = $request->getUploadedFiles()['attachedFile'];

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $failedInsertions = 0;
            $succededInsertions = 0;

            $fileStream = $uploadedFile->getStream();
            $csvData = array_map('str_getcsv', explode("\n", $fileStream->getContents()));

            foreach ($csvData as $row) {
                $product = new Product();
                $product->name = $row[1];
                $product->price = $row[2];
                $product->delay = $row[3];
                $product->productType = $row[4];
                $product->creationDate = $row[5];

                if ($product->insertProduct() != null) {
                    $succededInsertions += 1;
                } else {
                    $failedInsertions += 1;
                }
            }

            return ResponseHelper::jsonResponse($response, ["response" => "$succededInsertions fila/s cargadas con exito. $failedInsertions erroneas"]);
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Ocurrio un error al cargar el archivo"]);
    }

    public function GetAll($request, $response, $args)
    {
        $message = Product::getAllProducts();

        return ResponseHelper::jsonResponse($response, ["response" => $message]);
    }

    public function Delete($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $disabledValue = (int) $data["value"];
        if (($disabledValue === 0 || $disabledValue === 1) && (int) $args['id'] > 0) {
            $message = Product::modifyDisabledStatus($args['id'], $disabledValue, date('Y-m-d H:i:s')) ? "Producto modificado con exito" : "Ocurrio un error al modificar el producto";
            return ResponseHelper::jsonResponse($response, ["response" => $message]);
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function GetById($request, $response, $args)
    {
        $productId = (int) $args['id'];
        if ($productId > 0) {
            $product = Product::getProductById((int) $args['id']);
            if ($product instanceof Product) {
                return ResponseHelper::jsonResponse($response, ["response" => $product]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

}