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

class syruppay_token_claims_elements_PayableCard extends syruppay_token_PropertyMapper
{
    protected $cardNo;
    protected $expireDate;
    protected $cardIssuer;
    protected $cardIssuerName;
    protected $cardName;
    protected $cardNameInEnglish;
    protected $cardAcquirer;
    protected $cardType;

    public function setCardNo($cardNo)
    {
        if (empty($cardNo))
        {
            throw new InvalidArgumentException("cardNo shouldn't be null and not empty.");
        }

        $this->cardNo = $cardNo;
        return $this;
    }

    public function setExpireDate($expireDate)
    {
        if (empty($expireDate))
        {
            throw new InvalidArgumentException("expireDate shouldn't be null and not empty.");
        }

        $this->expireDate = $expireDate;
        return $this;
    }

    public function setCardIssuer($cardIssuer)
    {
        if (empty($cardIssuer))
        {
            throw new InvalidArgumentException("cardIssuer shouldn't be null and not empty.");
        }

        $this->cardIssuer = $cardIssuer;
        return $this;
    }

    public function setCardIssuerName($cardIssuerName)
    {
        if (empty($cardIssuerName))
        {
            throw new InvalidArgumentException("cardIssuerName shouldn't be null and not empty.");
        }

        $this->cardIssuerName = $cardIssuerName;
        return $this;
    }

    public function setCardName($cardName)
    {
        if (empty($cardName))
        {
            throw new InvalidArgumentException("cardNo shouldn't be null and not empty.");
        }

        $this->cardName = $cardName;
        return $this;
    }

    public function setCardNameInEnglish($cardNameInEnglish)
    {
        $this->cardNameInEnglish = $cardNameInEnglish;
        return $this;
    }

    public function setCardAcquirer($cardAcquirer)
    {
        if (empty($cardAcquirer))
        {
            throw new InvalidArgumentException("cardAcquirer shouldn't be null and not empty.");
        }

        $this->cardAcquirer = $cardAcquirer;
        return $this;
    }

    public function setCardType($cardType)
    {
        if (!in_array($cardType, getCardTypes()))
        {
            throw new InvalidArgumentException("cardType shouldn't be null and it should be one of CardType constants.");
        }

        $this->cardType = $cardType;
        return $this;
    }

    public static function of()
    {
        return new syruppay_token_claims_elements_PayableCard();
    }

}
