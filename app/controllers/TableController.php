<?php
require_once 'models/Table.php';

class TableController
{

    public function addTable()
    {
        $newTable = new Table();
        $newTableId = TableController::generateTableId();
        $newTable->setId($newTableId);
        $newTable->setStatus("Cerrada");
        $newTable->setCreationDate(date('Y-m-d H:i:s'));

        return $newTable->insertTable() ? $newTableId : 0;
    }


    public function getTables()
    {
        $allTables = Table::getAllTables();

        if (count($allTables) > 0) {
            $tablesData = array_map(function ($table) {
                return TableController::getTableData($table);
            }, $allTables);
        }

        return $tablesData;
    }

    public function updateTable($data, $tableId)
    {
        if (isset($data['table'])) {
            $tableData = $data['table'];
            if (isset($tableData['status'])) {
                $tableStatus = $tableData['status'];
                if (strlen($tableId) === 5 && strlen($tableStatus) > 3) {
                    $newTable = new Table();
                    $newTable->setId($tableId);
                    $newTable->setStatus($tableStatus);
                    $newTable->setModificationDate(date('Y-m-d H:i:s'));
                    return $newTable->updateTable() ? "Mesa modificada con exito" : "No se pudo modificar la mesa";
                }
            }
        }
        return "Uno de los parametros no es valido";
    }

    public function modifyTableStatus($data, $tableId)
    {
        if (isset($data["value"])) {
            $disabledValue = (int) $data["value"];
            if (($disabledValue === 0 || $disabledValue === 1) && strlen($tableId) === 5) {
                $tableStatus = Table::modifyDisabledStatus($tableId, $disabledValue);

                return $tableStatus ? "Mesa modificada con exito" : "Ocurrio un error al modificar la mesa";
            }
        }

        return "Uno de los parametros no es valido";
    }

    public function getTableById($data)
    {
        if (isset($data['id'])) {
            $tableId = $data['id'];
            if (strlen($tableId) === 5) {
                $table = Table::getTableById($data['id']);
                if ($table !== null) {
                    return TableController::getTableData($table);
                }
            }
        }

        return "La mesa no existe.";
    }

    public static function getTableData($user)
    {
        return [
            'id' => $user->getId(),
            'status' => $user->getStatus(),
            'creationDate' => $user->getCreationDate(),
            'modificationDate' => $user->getModificationDate(),
            'disabled' => $user->getDisabled() === 1,
        ];
    }

    public static function generateTableId()
    {
        return "M" . substr(md5(uniqid(mt_rand(), true)), 0, 4);
    }
}