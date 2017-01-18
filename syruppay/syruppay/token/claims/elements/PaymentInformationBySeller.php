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

class syruppay_token_claims_elements_PaymentInformationBySeller extends syruppay_token_PropertyMapper
{
    /**
     * @var syruppay\token\claims\elements\syruppay_token_claims_elements_CardInstallmentInformation
     */
    protected $productTitle;
    protected $productUrls = array();
    protected $lang = "KO";
    protected $currencyCode = "KRW";
    protected $paymentAmt;
    protected $shippingAddress;
    protected $deliveryPhoneNumber;
    protected $deliveryName;
    protected $deliveryType;
    protected $cardInfoList = array();
    protected $bankInfoList = array();
    protected $isExchangeable;

    public function getProductTitle()
    {
        return $this->productTitle;
    }

    public function setProductTitle($productTitle)
    {
        $this->productTitle = $productTitle;
    }

    public function getProductUrls()
    {
        return $this->productUrls;
    }

    public function setProductUrls(array $productUrls)
    {
        $this->productUrls = array_merge($this->productUrls, $productUrls);
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode($currency)
    {
        $this->currencyCode = $currency;
    }

    public function getPaymentAmt()
    {
        return $this->paymentAmt;
    }

    public function setPaymentAmt($paymentAmt)
    {
        $this->paymentAmt = $paymentAmt;
    }

    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getDeliveryPhoneNumber()
    {
        return $this->deliveryPhoneNumber;
    }

    public function setDeliveryPhoneNumber($deliveryPhoneNumber)
    {
        $this->deliveryPhoneNumber = $deliveryPhoneNumber;
    }

    public function getDeliveryName()
    {
        return $this->deliveryName;
    }

    public function setDeliveryName($deliveryName)
    {
        $this->deliveryName = $deliveryName;
    }

    public function getDeliveryType()
    {
        return $this->deliveryType;
    }

    public function setDeliveryType($deliveryType)
    {
        $this->deliveryType = $deliveryType;
    }

    public function isExchangeable()
    {
        return $this->isExchangeable;
    }

    public function setExchangeable($isExchangeable)
    {
        $this->isExchangeable = $isExchangeable;
    }

    public function getCardInfoList()
    {
        return $this->cardInfoList;
    }

    public function setCardInfoList(array $cardInfoList)
    {
        $this->cardInfoList = array_merge($this->cardInfoList, $cardInfoList);
    }

    public function getBankInfoList()
    {
        return $this->bankInfoList;
    }

    public function setBankInfoList(array $bankInfoList)
    {
        $this->bankInfoList = array_merge($this->bankInfoList, $bankInfoList);
    }
}
