<?php
require_once 'models/Order.php';
require_once 'interfaces/IApiUsable.php';
require_once 'utils/ResponseHelper.php';

class OrderController implements IApiUsable
{

    public function Create($request, $response, $args)
    {
        $data = $request->getParsedBody();
        if (isset($data['data'])) {
            $orderData = $data['data'];
            if (isset($orderData['customerName'], $orderData['products'], $orderData['relatedTable'])) {

                $products = $orderData['products'];
                $customerName = $orderData['customerName'];
                $relatedTable = $orderData['relatedTable'];

                if (OrderController::checkValidIds($products) && strlen($customerName) > 3) {
                    $newOrder = new Order();
                    $newOrder->id = OrderController::generateOrderId();
                    foreach ($products as $product) {
                        $newOrder->customerName = $customerName;
                        $newOrder->productId = $product['productId'];
                        $newOrder->quantity = $product['quantity'];
                        $newOrder->relatedTable = $relatedTable;
                        $newOrder->status = "Pendiente";
                        $newOrder->creationDate = date('Y-m-d H:i:s');

                        $message = $newOrder->insertOrder();
                    }
                    return ResponseHelper::jsonResponse($response, ["response" => $message]);
                }

                return ResponseHelper::jsonResponse($response, ["response" => "Parametros invalidos"]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["response" => "Parametros faltantes"]);
    }

    public function Update($request, $response, $args)
    {
        $orderId = $args['id'];
        $productId = $args['productId'];
        $data = $request->getParsedBody();
        if (isset($data['data'])) {
            $orderData = $data['data'];
            if (isset($orderData['status']) && isset($orderData['estimatedDelay'])) {
                $orderStatus = $orderData['status'];
                $orderDelay = (int) $orderData['estimatedDelay'];
                if (strlen($orderStatus) > 3 && $orderDelay > 0) {
                    $newOrder = new Order();
                    $newOrder->id = $orderId;
                    $newOrder->productId = (int) $productId;
                    $newOrder->status = $orderStatus;
                    $newOrder->estimatedDelay = $orderDelay;
                    $newOrder->modificationDate = date('Y-m-d H:i:s');
                    $message = $newOrder->updateOrder() ? "Orden modificada con exito" : "No se pudo modificar la orden";
                    return ResponseHelper::jsonResponse($response, ["response" => $message]);
                }
            }
        }
        return ResponseHelper::jsonResponse($response, ["response" => "Uno de los parametros no es valido"]);
    }

    public function GetAll($request, $response, $args)
    {
        $orders = Order::getAllOrders();
        return ResponseHelper::jsonResponse($response, ["response" => $orders]);
    }

    public function GetById($request, $response, $args)
    {
        if (isset($args['id'])) {
            $orderId = $args['id'];
            if (strlen($orderId) === 5) {
                $orders = Order::getOrderById($args['id']);
                return ResponseHelper::jsonResponse($response, ["response" => $orders]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["response" => "La orden no existe"]);

    }

    public function Delete($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $orderId = $args['id'];
        $productId = $args['productId'];

        if (isset($data["value"])) {
            $disabledValue = (int) $data["value"];
            if (($disabledValue === 0 || $disabledValue === 1) && strlen($args['id']) === 5) {
                $message = Order::modifyDisabledStatus($orderId, $productId, $disabledValue) ? "Orden modificada con exito" : "Ocurrio un error al modificar la orden";
                return ResponseHelper::jsonResponse($response, ["response" => $message]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public static function generateOrderId()
    {
        return "P" . substr(md5(uniqid(mt_rand(), true)), 0, 4);
    }

    public static function checkValidIds($products)
    {
        foreach ($products as $p) {
            if (!is_numeric($p['productId']) && !is_numeric($p['quantity']) && $p['quantity'] > 0) {
                return false;
            }
        }
        return true;
    }
}