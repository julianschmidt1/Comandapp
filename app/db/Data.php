<?php

class Data
{
    private static $dataObject;
    private $pdoObject;

    private function __construct()
    {
        try {
            $this->pdoObject = new PDO('mysql:host=localhost;dbname=comanda;charset=utf8', 'root', '', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->pdoObject->exec("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            print "Oncurrio un error: " . $e->getMessage();
            die();
        }
    }

    public function getQuery($sql)
    {
        return $this->pdoObject->prepare($sql);
    }

    public function getLastInsertedId()
    {
        return $this->pdoObject->lastInsertId();
    }

    public static function getDataObject()
    {
        if (!isset(self::$dataObject)) {
            self::$dataObject = new Data();
        }
        return self::$dataObject;
    }

    public function __clone()
    {
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
    }
}
?>