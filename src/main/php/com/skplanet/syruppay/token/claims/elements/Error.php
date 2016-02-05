<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-06
 * Time: 오전 1:09
 */

namespace com\skplanet\syruppay\token\claims\elements;


use com\skplanet\syruppay\token\utils\ClassPropertyUtils;

class Error implements Element
{
    use ClassPropertyUtils;

    private $type;
    private $description;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function validRequired()
    {
        if (!isset($this->type) || !isset($this->description)) {
            throw new \InvalidArgumentException("Error object couldn't be with null fields type : ".$this->type.", description : ".$this->description);
        }
    }
}

class ErrorType
{
    const MAINTENACE = 'MAINTENACE';
    const SYSTEM_ERR = 'SYSTEM_ERR';
}
