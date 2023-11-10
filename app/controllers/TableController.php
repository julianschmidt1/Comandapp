<?php
require_once 'models/Table.php';
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

        $message = $newTable->insertTable() ? "Tabla creada con exito" : "Ocurrio un error al crear la tabla";
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
        if (isset($data['table'])) {
            $tableData = $data['table'];
            if (isset($tableData['status'])) {
                $tableStatus = $tableData['status'];
                if (strlen($args['id']) === 5 && strlen($tableStatus) > 3) {
                    $newTable = new Table();
                    $newTable->id = $args['id'];
                    $newTable->status = $tableStatus;
                    $newTable->modificationDate = date('Y-m-d H:i:s');
                    $message = $newTable->updateTable() ? "Mesa modificada con exito" : "No se pudo modificar la mesa";
                    return ResponseHelper::jsonResponse($response, ["response" => $message]);
                }
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function Delete($request, $response, $args)
    {
        $data = $request->getParsedBody();
        if (isset($data["value"])) {
            $disabledValue = (int) $data["value"];
            if (($disabledValue === 0 || $disabledValue === 1) && strlen($args['id']) === 5) {
                $tableStatus = Table::modifyDisabledStatus($args['id'], $disabledValue);

                $message = $tableStatus ? "Mesa modificada con exito" : "Ocurrio un error al modificar la mesa";
                return ResponseHelper::jsonResponse($response, ["response" => $message]);
            }
        }

        return ResponseHelper::jsonResponse($response, ["error" => "Uno de los parametros no es valido"]);
    }

    public function GetById($request, $response, $args)
    {
        if (isset($data['id'])) {
            $tableId = $args['id'];
            if (strlen($tableId) === 5) {
                $table = Table::getTableById($args['id']);
                if ($table instanceof Table) {
                    return ResponseHelper::jsonResponse($response, ["error" => $table]);
                }
            }
        }

        return "La mesa no existe.";
    }

    public static function generateTableId()
    {
        return "M" . substr(md5(uniqid(mt_rand(), true)), 0, 4);
    }
}