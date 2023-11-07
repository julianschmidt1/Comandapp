<?php

class BaseModel
{
    private $_id;
    private $_creationDate;
    private $_modificationDate;
    private $_disabled;

    public function setCreationDate($creationDate)
    {
        $this->_creationDate = $creationDate;
    }

    public function setModificationDate($modificationDate)
    {
        $this->_modificationDate = $modificationDate;
    }

    public function setDisabled($disabled)
    {
        $this->_disabled = $disabled;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getCreationDate()
    {
        return $this->_creationDate;
    }

    public function getModificationDate()
    {
        return $this->_modificationDate;
    }

    public function getDisabled()
    {
        return $this->_disabled;
    }
}

?>