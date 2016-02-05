<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-06
 * Time: 오전 1:08
 */

namespace com\skplanet\syruppay\token\claims\elements;


use com\skplanet\syruppay\token\utils\ClassPropertyUtils;

class Accept implements Element
{
    use ClassPropertyUtils;

    private $type;
    private $conditions = array();

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
        return $this;
    }

    function validRequired()
    {
        if (!isset($type)) {
            throw new \InvalidArgumentException("Accept object couldn't be with null fields.");
        }

        if (!isset($this->conditions) || empty($this->conditions)) {
            throw new \InvalidArgumentException("Conditions of Accept object couldn't be empty. you should contain with conditions of Accept object.");
        }
    }
}

class AcceptType
{
    const CARD = 'CARD';
    const SYRUP_PAY_COUPON = 'const';
}
