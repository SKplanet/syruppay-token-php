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

class syruppay_token_claims_elements_ShippingAddress extends syruppay_token_PropertyMapper
{
    protected $id;
    protected $userActionCode;
    protected $name;
    protected $countryCode;
    protected $zipCode;
    protected $mainAddress;
    protected $detailAddress;
    protected $city;
    protected $state;
    protected $recipientName;
    protected $recipientPhoneNumber;
    protected $deliveryRestriction;
    protected $defaultDeliveryCost;
    protected $additionalDeliveryCost;
    protected $orderApplied;

    public function __construct()
    {
        $argNumbers = func_num_args();
        if ($argNumbers == 0) {
            return;
        } else if ($argNumbers == 6) {
            $args = func_get_args();
            $this->zipCode = $args[0];
            $this->mainAddress = $args[1];
            $this->detailAddress = $args[2];
            $this->city = $args[3];
            $this->state = $args[4];
            $this->countryCode = $this->setCountryCode($args[5])->getCountryCode();
        } else {
            throw new InvalidArgumentException("usage : new ShippingAddress(zipCode, mainAddress, detailAddress, city, state, countryCode)");
        }
    }

    public function getUserActionCode()
    {
        return $this->userActionCode;
    }

    public function setUserActionCode($userActionCode)
    {
        $this->userActionCode = $userActionCode;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public function setCountryCode($countryCode)
    {
        if (!syruppay_token_claims_PayConfigurer::isValidCountryAlpha2Code($countryCode)) {
            throw new InvalidArgumentException("countryCode should meet the specifications of ISO-3166 Alpha2(as KR, US) except prefix like a2. yours : " . $this->countryCode);
        }
        $this->countryCode = strtolower($countryCode);
        return $this;
    }

    public function getZipCode()
    {
        return $this->zipCode;
    }

    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getMainAddress()
    {
        return $this->mainAddress;
    }

    public function setMainAddress($mainAddress)
    {
        $this->mainAddress = $mainAddress;
        return $this;
    }

    public function getDetailAddress()
    {
        return $this->detailAddress;
    }

    public function setDetailAddress($detailAddress)
    {
        $this->detailAddress = $detailAddress;
        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    public function getRecipientName()
    {
        return $this->recipientName;
    }

    public function setRecipientName($recipientName)
    {
        $this->recipientName = $recipientName;
        return $this;
    }

    public function getRecipientPhoneNumber()
    {
        return $this->recipientPhoneNumber;
    }

    public function setRecipientPhoneNumber($recipientPhoneNumber)
    {
        if (!isset($recipientPhoneNumber) || !preg_match_all('/^([0-9]*)$/', $recipientPhoneNumber, $match)) {
            throw new InvalidArgumentException("phone number should be contained numbers. remove characters as '-'. yours : " . $recipientPhoneNumber);
        }

        $this->recipientPhoneNumber = $recipientPhoneNumber;
        return $this;
    }

    public function getDeliveryRestriction()
    {
        return $this->deliveryRestriction;
    }

    public function setDeliveryRestriction($deliveryRestriction)
    {
        $this->deliveryRestriction = $deliveryRestriction;
        return $this;
    }

    public function getDefaultDeliveryCost()
    {
        return $this->defaultDeliveryCost;
    }

    public function setDefaultDeliveryCost($defaultDeliveryCost)
    {
        $this->defaultDeliveryCost = $defaultDeliveryCost;
        return $this;
    }

    public function getAdditionalDeliveryCost()
    {
        return $this->additionalDeliveryCost;
    }

    public function setAdditionalDeliveryCost($additionalDeliveryCost)
    {
        $this->additionalDeliveryCost = $additionalDeliveryCost;
        return $this;
    }

    public function getOrderApplied()
    {
        return $this->orderApplied;
    }

    public function setOrderApplied($orderApplied)
    {
        $this->orderApplied = $orderApplied;
        return $this;
    }

    public function mapToStringForFds()
    {
        return $this->countryCode . "|" . $this->zipCode . "|" . $this->mainAddress . "|" . $this->detailAddress . "|" . $this->city . "|" . $this->state . "|";
    }

    public function validRequiredToCheckout()
    {
        if (!isset($this->id) || !isset($this->name) ||
            !isset($this->countryCode) || !isset($this->zipCode) ||
            !isset($this->mainAddress) || !isset($this->detailAddress) ||
            !isset($this->recipientName) || !isset($this->recipientPhoneNumber)
        ) {
            throw new InvalidArgumentException("ShippingAddress object to checkout couldn't be with null fields. id : " . $this->id . ", name : " . $this->name . ", countryCode : " . $this->countryCode . ", zipCode : " . $this->zipCode . ", mainAddress : " . $this->mainAddress . ", detailAddress : " . $this->detailAddress . ", recipientName : " . $this->recipientName . ", recipientPhoneNumber : " . $this->recipientPhoneNumber);
        }

        if (!syruppay_token_claims_PayConfigurer::isValidCountryAlpha2Code($this->countryCode)) {
            throw new InvalidArgumentException("countryCode should meet the specifications of ISO-3166 Alpha2(as KR, US) except prefix like a2. yours : " . $this->countryCode);
        }

        if ($this->defaultDeliveryCost <= 0) {
            throw new InvalidArgumentException("defaultDeliveryCost field should be bigger than 0. yours : " . $this->defaultDeliveryCost);
        }
    }
}
