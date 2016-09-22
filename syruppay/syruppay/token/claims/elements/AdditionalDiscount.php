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

class AdditionalDiscount extends PropertyMapper implements Element
{
    protected $percentOff;
    protected $maxApplicableAmt;

    public function getPercentOff()
    {
        return $this->percentOff;
    }

    public function setPercentOff($percentOff)
    {
        if ($percentOff <= 0) {
            throw new \InvalidArgumentException("percentOff field should be bigger than 0. yours percentOff is : " . $percentOff);
        }
        $this->percentOff = $percentOff;
        return $this;
    }

    public function getMaxApplicableAmt()
    {
        return $this->maxApplicableAmt;
    }

    public function setMaxApplicableAmt($maxApplicableAmt)
    {
        if ($maxApplicableAmt <= 0) {
            throw new \InvalidArgumentException("maxApplicableAmt field should be bigger than 0. yours maxApplicableAmt is : " . $maxApplicableAmt);
        }
        $this->maxApplicableAmt = $maxApplicableAmt;
        return $this;
    }

    public function validRequired()
    {
        if ($this->percentOff <= 0) {
            throw new \InvalidArgumentException("percentOff field should be bigger than 0. yours percentOff is : " . $this->percentOff);
        }
        if ($this->maxApplicableAmt <= 0) {
            throw new \InvalidArgumentException("maxApplicableAmt field should be bigger than 0. yours maxApplicableAmt is : " . $this->maxApplicableAmt);
        }
    }
}
