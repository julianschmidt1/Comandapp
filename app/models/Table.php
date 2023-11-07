<?php
require_once 'db/Data.php';
require_once 'models/BaseModel.php';

class Table extends BaseModel {
    public $id;
    public $status;

    public function insertTable(){
        $dataObject = Data::getDataObject();
        $query = $dataObject->getQuery(
            "INSERT INTO tables (id, status, creation_date)
            VALUES ('$this->id', '$this->status', '$this->creationDate')"
        );
        $query->execute();
        return $dataObject->getLastInsertedId();
    }
}