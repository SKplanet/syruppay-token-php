<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-06
 * Time: 오전 1:10
 */

namespace com\skplanet\syruppay\token\claims\elements;


use com\skplanet\syruppay\token\PropertyMapper;

class MonthlyInstallment extends PropertyMapper implements Element
{
    protected $cardCode;
    protected $conditions = array();

    public function getCardCode()
    {
        return $this->cardCode;
    }

    public function setCardCode($cardCode)
    {
        $this->cardCode = $cardCode;
        return this;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function addConditionIncludeMax($min, $includeMin, $max, $includeMax, $monthlyInstallmentInfo)
    {
        $m = array();
        $m["paymentAmtRange"] = ($includeMin ? "[" : "(") . $min . "-" . $max . ($includeMax ? "]" : ")");
        $m["monthlyInstallmentInfo"] = $monthlyInstallmentInfo;
        $this->conditions[] = $m;
        return $this;
    }

    public function addCondition($min, $includeMin, $monthlyInstallmentInfo)
    {
        $m = array();
        $m["paymentAmtRange"] = ($includeMin ? "[" : "(") . $min . "-]";
        $m["monthlyInstallmentInfo"] = $monthlyInstallmentInfo;
        $this->conditions[] = $m;
        return $this;
    }

    public function validRequired()
    {
        if (!isset($this->cardCode)) {
            throw new \InvalidArgumentException("MonthlyInstallment object couldn't be with null fields cardCode is null");
        }

        if (!isset($this->conditions) || empty($this->conditions)) {
            throw new \InvalidArgumentException("Conditions of MonthlyInstallment object couldn't be empty. you should contain with conditions of MonthlyInstallment object by addCondition method.");
        }
    }
}

