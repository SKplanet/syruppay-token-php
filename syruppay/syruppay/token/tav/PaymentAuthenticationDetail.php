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

class syruppay_token_tav_PaymentAuthenticationDetail extends syruppay_token_PropertyMapper
{
    protected $payMethod;
    protected $payAmount;
    protected $offerAmount;
    protected $loyaltyAmount;
    protected $payInstallment;
    protected $payCurrency;
    protected $payFinanceCode;
    protected $isCardPointApplied;

    public function getPayMethod()
    {
        return $this->payMethod;
    }

    public function setPayMethod($payMethod)
    {
        $this->payMethod = $payMethod;
    }

    public function getPayAmount()
    {
        return $this->payAmount;
    }

    public function setPayAmount($payAmount)
    {
        $this->payAmount = $payAmount;
    }

    public function getOfferAmount()
    {
        return $this->offerAmount;
    }

    public function setOfferAmount($offerAmount)
    {
        $this->offerAmount = $offerAmount;
    }

    public function getLoyaltyAmount()
    {
        return $this->loyaltyAmount;
    }

    public function setLoyaltyAmount($loyaltyAmount)
    {
        $this->loyaltyAmount = $loyaltyAmount;
    }

    public function getPayInstallment()
    {
        return $this->payInstallment;
    }

    public function setPayInstallment($payInstallment)
    {
        $this->payInstallment = $payInstallment;
    }

    public function getPayCurrency()
    {
        return $this->payCurrency;
    }

    public function setPayCurrency($payCurrency)
    {
        $this->payCurrency = $payCurrency;
    }

    public function getPayFinanceCode()
    {
        return $this->payFinanceCode;
    }

    public function setPayFinanceCode($payFinanceCode)
    {
        $this->payFinanceCode = $payFinanceCode;
    }

    public function getIsCardPointApplied()
    {
        return $this->isCardPointApplied;
    }

    public function setIsCardPointApplied($isCardPointApplied)
    {
        $this->isCardPointApplied = $isCardPointApplied;
    }

    public function toJson()
    {
        $thisArray = $this->__toArray();
        return json_encode($thisArray);
    }
}
