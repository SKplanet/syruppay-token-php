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
namespace syruppay\token\claims\elements;

use syruppay\token\PropertyMapper;

class Accept extends PropertyMapper implements Element
{
    protected $type;
    protected $conditions = array();

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

    public function addConditions($cardCode, $minPaymentAmt)
    {
        $this->conditions[] = array('cardCode' => $cardCode, 'minPaymentAmt' => $minPaymentAmt);
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
