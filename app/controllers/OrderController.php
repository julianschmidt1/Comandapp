<?php
require_once 'models/Order.php';

class OrderController
{

    public function addOrder($data)
    {
        if (isset($data['data'])) {
            $orderData = $data['data'];
            if (isset($orderData['customerName']) && isset($orderData['productIds'])) {

                $productIds = $orderData['productIds'];
                $customerName = $orderData['customerName'];

                if (OrderController::checkValidIds($productIds) && strlen($customerName) > 3) {
                    $newOrder = new Order();
                    $newOrder->id = OrderController::generateOrderId();
                    foreach ($productIds as $id) {
                        $newOrder->customerName = $customerName;
                        $newOrder->productId = $id;
                        $newOrder->status = "Pendiente";
                        $newOrder->creationDate = date('Y-m-d H:i:s');

                        $newOrder->insertOrder();
                    }
                    return $newOrder->id;
                }

                return "Parametros invalidos";
            }

        }
        return "Parametros faltantes";
    }

    public function updateOrder($data, $orderId, $productId)
    {
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
                    return $newOrder->updateOrder() ? "Orden modificada con exito" : "No se pudo modificar la orden";
                }
            }
        }
        return "Uno de los parametros no es valido";
    }

    public function getOrders()
    {
        return Order::getAllOrders();
    }

    public function getOrderById($data)
    {
        if (isset($data['id'])) {
            $orderId = $data['id'];
            if (strlen($orderId) === 5) {
                $orders = Order::getOrderById($data['id']);
                if (count($orders) > 0) {
                    return $orders;
                }
            }
        }

        return "La orden no existe.";
    }

    public static function generateOrderId()
    {
        return "P" . substr(md5(uniqid(mt_rand(), true)), 0, 4);
    }

    public static function checkValidIds($ids)
    {
        foreach ($ids as $id) {
            if (!is_numeric($id)) {
                return false;
            }
        }
        return true;
    }
}