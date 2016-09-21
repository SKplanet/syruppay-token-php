<?php
/*
 * The MIT License (MIT)
 * Copyright (c) 2015 SK PLANET. All Rights Reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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

    public function setCardCodes(array $cardCodes)
    {
        foreach ($cardCodes as $cardCode)
        {
            if (isset($this->cardCode))
            {
                $this->cardCode .= ':';
            }
            $this->cardCode .= $cardCode;
        }
        return $this;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function addConditionMinAndMax($min, $includeMin, $max, $includeMax, $monthlyInstallmentInfo)
    {
        $m = array();
        $m["paymentAmtRange"] = ($includeMin ? "[" : "(") . $min . "-" . $max . ($includeMax ? "]" : ")");
        $m["monthlyInstallmentInfo"] = $monthlyInstallmentInfo;
        $this->conditions[] = $m;
        return $this;
    }

    public function addConditionMin($min, $includeMin, $monthlyInstallmentInfo)
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

