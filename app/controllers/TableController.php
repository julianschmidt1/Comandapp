<?php
require_once 'models/Table.php';
require_once 'models/Order.php';
require_once 'interfaces/IApiUsable.php';
require_once 'utils/ResponseHelper.php';

class TableController implements IApiUsable
{

    public function Create($request, $response, $args)
    {
        $newTable = new Table();
        $newTableId = TableController::generateTableId();
        $newTable->id = $newTableId;
        $newTable->status = "Cerrada";
        $newTable->creationDate = date('Y-m-d H:i:s');

        $message = $newTable->insertTable() ? "Mesa creada con exito" : "Ocurrio un error al crear la mesa";
        return ResponseHelper::jsonResponse($response, ["response" => $message]);
    }


    public function GetAll($request, $response, $args)
    {
        $message = Table::getAllTables();
        return ResponseHelper::jsonResponse($response, ["response" => $message]);
    }

    public function Update($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $userType = $request->getAttribute('userType');
        $tableStatus = $data['status'];
        if (
            strlen($args['id']) === 5 &&
            (($userType === 4 || $userType === 5) &&
                ($tableStatus === "con cliente esperando pedido" || $tableStatus === "con cliente comiendo" || $tableStatus === "con cliente pagando"))
            || ($userType === 5 && $tableStatus === "cerrada") || ($userType === 5 && $tableStatus === "pendiente")
        ) {
            $newTable = new Table();
            $newTable->id = $args['id'];
            $newTable->status = $tableStatus;
            $newTable->modificationDate = date('Y-m-d H:i:s');
            $message = $newTable->updateTable() ? "Mesa modificada con exito" : "No se pudo modificar la mesa";
            return ResponseHelper::jsonResponse($response, ["response" => $message]);
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function Delete($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $disabledValue = (int) $data["value"];
        if (($disabledValue === 0 || $disabledValue === 1) && strlen($args['id']) === 5) {
            $tableStatus = Table::modifyDisabledStatus($args['id'], $disabledValue, date('Y-m-d H:i:s'));

            $message = $tableStatus ? "Mesa modificada con exito" : "Ocurrio un error al modificar la mesa";
            return ResponseHelper::jsonResponse($response, ["response" => $message]);
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function CreateReview($request, $response, $args)
    {
        $data = $request->getParsedBody();

        if (
            Table::getTableById($data['tableId']) instanceof Table &&
            count(Order::getOrderById($data['orderId'])) >= 1 &&
            self::isValidRating((int) $data['tableRating']) &&
            self::isValidRating((int) $data['restaurantRating']) &&
            self::isValidRating((int) $data['waiterRating']) &&
            self::isValidRating((int) $data['chefRating']) &&
            (strlen($data['description']) >= 2 && strlen($data['description']) <= 66)
        ) {

            if (
                Table::insertTableReview(
                    $data['tableId'],
                    $data['orderId'],
                    (int) $data['tableRating'],
                    (int) $data['restaurantRating'],
                    (int) $data['waiterRating'],
                    (int) $data['chefRating'],
                    $data['description'],
                    date('Y-m-d H:i:s')
                )
            ) {
                return ResponseHelper::jsonResponse($response, ["response" => "Reseña creada con exito"]);
            }
            return ResponseHelper::jsonResponse($response, ["response" => "Ocurrio un error al crear la reseña"]);
        }

        return ResponseHelper::jsonResponse($response, ["response" => "Parametros invalidos"]);
    }

    public function GetBestReviews($request, $response, $args)
    {
        $topReviews = Table::getBestReviews();

        try {
            return ResponseHelper::jsonResponse($response, ["response" => $topReviews]);
        } catch (PDOException $e) {
            return ResponseHelper::jsonResponse($response, ["response" => "Ocurrio un error al mostrar las mejores reseñas"]);
        }
    }

    public function GetMostUsed($request, $response, $args)
    {
        try {
            $mostUsedTable = Table::getMostUsed();
            return ResponseHelper::jsonResponse($response, ["response" => $mostUsedTable]);
        } catch (PDOException $e) {
            return ResponseHelper::jsonResponse($response, ["error" => "Ocurrio un error al mostrar los datos"]);
        }
    }

    public function GetById($request, $response, $args)
    {
        $tableId = $args['id'];
        if (strlen($tableId) === 5) {
            $table = Table::getTableById($args['id']);
            if ($table instanceof Table) {
                return ResponseHelper::jsonResponse($response, ["response" => $table]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "La mesa no existe"]);
    }

    public static function isValidRating($rating)
    {
        return $rating >= 1 && $rating <= 10;
    }

    public static function generateTableId()
    {
        return "M" . substr(md5(uniqid(mt_rand(), true)), 0, 4);
    }
}