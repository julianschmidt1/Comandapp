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
                    $newOrder->setId(OrderController::generateOrderId());
                    foreach ($productIds as $id) {
                        $newOrder->setCustomerName($customerName);
                        $newOrder->setProductId($id);
                        $newOrder->setStatus("Pendiente");
                        $newOrder->setCreationDate(date('Y-m-d H:i:s'));

                        $newOrder->insertOrder();
                    }
                    return "Order cargada con exito";
                }

                return "Parametros invalidos";
            }

        }
        return "Parametros faltantes";
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