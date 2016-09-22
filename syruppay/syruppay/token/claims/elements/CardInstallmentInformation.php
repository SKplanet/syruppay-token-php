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

class CardInstallmentInformation extends PropertyMapper
{
    protected $cardCode;
    protected $monthlyInstallmentInfo;

    public function __construct()
    {
        $argNumbers = func_num_args();
        if ($argNumbers == 0) {
            return;
        } else if ($argNumbers == 2) {
            $args = func_get_args();
            $this->cardCode = $args[0];
            $this->monthlyInstallmentInfo = $args[1];
        } else {
            throw new \InvalidArgumentException("usage : new CardInstallmentInformation(cardCode, monthlyInstallmentInfo)");
        }
    }

    public function getCardCode()
    {
        return $this->cardCode;
    }

    public function getMonthlyInstallmentInfo()
    {
        return $this->monthlyInstallmentInfo;
    }
}
