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

class ProductDeliveryInfo extends PropertyMapper implements Element
{
    protected $deliveryType;
    protected $deliveryName;
    protected $defaultDeliveryCostApplied;
    protected $additionalDeliveryCostApplied;
    protected $shippingAddressDisplay;

    public function isShippingAddressDisplay()
    {
        return $this->shippingAddressDisplay;
    }

    public function setShippingAddressDisplay($shippingAddressDisplay)
    {
        $this->shippingAddressDisplay = $shippingAddressDisplay;
        return $this;
    }

    public function getDeliveryType()
    {
        return $this->deliveryType;
    }

    public function setDeliveryType($deliveryType)
    {
        $this->deliveryType = $deliveryType;
        return $this;
    }

    public function getDeliveryName()
    {
        return $this->deliveryName;
    }

    public function setDeliveryName($deliveryName)
    {
        $this->deliveryName = $deliveryName;
        return $this;
    }

    public function isDefaultDeliveryCostApplied()
    {
        return $this->defaultDeliveryCostApplied;
    }

    public function setDefaultDeliveryCostApplied($defaultDeliveryCostApplied)
    {
        $this->defaultDeliveryCostApplied = $defaultDeliveryCostApplied;
        return $this;
    }

    public function isAdditionalDeliveryCostApplied()
    {
        return $this->additionalDeliveryCostApplied;
    }

    public function setAdditionalDeliveryCostApplied($additionalDeliveryCostApplied)
    {
        $this->additionalDeliveryCostApplied = $additionalDeliveryCostApplied;
        return $this;
    }

    public function validRequired()
    {
        if (!isset($this->deliveryType) || !isset($this->deliveryName)) {
            throw new \InvalidArgumentException("ProductDeliveryInfo object couldn't be with null fields. deliveryType : " . $this->deliveryType . ", deliveryName : " . $this->deliveryName);
        }
    }
}

class DeliveryType
{
    const PREPAID = 'PREPAID';
    const FREE = 'FREE';
    const DIY = 'DIY';
    const QUICK = 'QUICK';
    const PAYMENT_ON_DELIVERY = 'PAYMENT_ON_DELIVERY';
}
