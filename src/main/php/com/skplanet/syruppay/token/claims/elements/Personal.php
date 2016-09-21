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

class Personal extends PropertyMapper
{
    protected $username;
    protected $lineNumber;
    protected $operatorCode;
    protected $ssnFirst7Digit;
    protected $email;
    protected $ciHash;
    /**
     * @var com\skplanet\syruppay\token\claims\elements\PayableCard
     */
    protected $payableCard;

    public function setUsername($username)
    {
        if (empty($username))
        {
            throw new \InvalidArgumentException("username shouldn't be null and not empty.");
        }

        $this->username = $username;
        return $this;
    }

    public function setLineNumber($lineNumber)
    {
        if (empty($lineNumber))
        {
            throw new \InvalidArgumentException("lineNumber shouldn't be null and not empty.");
        }
        $this->lineNumber = $lineNumber;
        return $this;
    }

    public function setOperatorCode($operatorCode)
    {
        $this->operatorCode = $operatorCode;
        return $this;
    }

    public function setSsnFirst7Digit($ssnFirst7Digit)
    {
        if (empty($ssnFirst7Digit)) {
            throw new \InvalidArgumentException("ssnFirst7Digit shouldn't be null and not empty.");
        }

        if (strlen($ssnFirst7Digit) != 7)
        {
            throw new \InvalidArgumentException("length of ssnFirst7Digit should be 7. yours inputs is : $ssnFirst7Digit");
        }

        $this->ssnFirst7Digit = $ssnFirst7Digit;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setCiHash($ciHash)
    {
        $this->ciHash = $ciHash;
        return $this;
    }

    public function setPayableCard($payableCard)
    {
        $this->payableCard = $payableCard;
        return $this;
    }

    public static function of()
    {
        return new Personal();
    }
}
