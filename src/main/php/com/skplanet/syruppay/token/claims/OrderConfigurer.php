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

namespace com\skplanet\syruppay\token\claims;


use com\skplanet\syruppay\token\claims\elements\ProductDeliveryInfo;

class OrderConfigurer extends AbstractTokenConfigurer
{
    protected $productPrice;
    protected $submallName;
    protected $privacyPolicyRequirements;
    protected $mainShippingAddressSettingDisabled;
    /**
     * @var com\skplanet\syruppay\token\claims\elements\ProductDeliveryInfo
     */
    protected $productDeliveryInfo;
    /**
     * @var com\skplanet\syruppay\token\claims\elements\Offer
     */
    protected $offerList = array();
    /**
     * @var com\skplanet\syruppay\token\claims\elements\Loyalty
     */
    protected $loyaltyList = array();
    /**
     * @var com\skplanet\syruppay\token\claims\ShippingAddress
     */
    protected $shippingAddressList = array();
    /**
     * @var com\skplanet\syruppay\token\claims\elements\MonthlyInstallment
     */
    protected $monthlyInstallmentList = array();

    function __construct()
    {
        $this->productDeliveryInfo = new ProductDeliveryInfo();
    }

    public function getMonthlyInstallmentList()
    {
        return $this->monthlyInstallmentList;
    }

    public function getPrivacyPolicyRequirements()
    {
        return $this->privacyPolicyRequirements;
    }

    public function isMainShippingAddressSettingDisabled()
    {
        return $this->mainShippingAddressSettingDisabled;
    }

    public function getProductPrice()
    {
        return $this->productPrice;
    }

    public function getSubmallName()
    {
        return $this->submallName;
    }

    public function getProductDeliveryInfo()
    {
        return $this->productDeliveryInfo;
    }

    function claimName()
    {
        return "checkoutInfo";
    }

    function validRequired()
    {
        if ($this->productPrice <= 0) {
            throw new \InvalidArgumentException("product price field couldn't be zero. check yours input value : " . $this->productPrice);
        }
        if (!isset($this->productDeliveryInfo)) {
            throw new \InvalidArgumentException("you should contain ProductDeliveryInfo object.");
        }

        $this->productDeliveryInfo->validRequired();

        foreach ($this->offerList as $offer) {
            if (is_object($offer) && $offer instanceof Offer) {
                $offer->validRequired();
            }
        }
        foreach ($this->loyaltyList as $loyalty) {
            if (is_object($loyalty) && $loyalty instanceof Loyalty) {
                $loyalty->validRequired();
            }
        }
        foreach ($this->shippingAddressList as $shippingAddress) {
            if (is_object($shippingAddress) && $shippingAddress instanceof ShippingAddress) {
                $shippingAddress->validRequiredToCheckout();
            }
        }
        foreach ($this->monthlyInstallmentList as $monthlyInstallment) {
            if (is_object($monthlyInstallment) && $monthlyInstallment instanceof MonthlyInstallment) {
                $monthlyInstallment->validRequired();
            }
        }
    }

    public function withPrivacyPolicyRequirements($privacyPolicyRequirements)
    {
        $this->privacyPolicyRequirements = $privacyPolicyRequirements;
        return $this;
    }

    public function disableMainShippingAddressSetting()
    {
        $this->mainShippingAddressSettingDisabled = true;
        return $this;
    }

    public function enableMainShippingAddressSetting()
    {
        $this->mainShippingAddressSettingDisabled = false;
        return $this;
    }

    public function withShippingAddresses(array $shippingAddresses)
    {
        foreach ($shippingAddresses as $shippingAddress) {
            if (is_object($shippingAddress) && $shippingAddress instanceof ShippingAddress) {
                $shippingAddress->validRequiredToCheckout();
            }
        }
        $this->shippingAddressList = array_merge($this->shippingAddressList, $shippingAddresses);
        return $this;
    }

    public function withProductPrice($productPrice)
    {
        if ($productPrice <= 0) {
            throw new \InvalidArgumentException("Cannot be smaller than 0. Check yours input value : " . $this->productPrice);
        }
        $this->productPrice = $productPrice;
        return $this;
    }

    public function withSubmallName($submallName)
    {
        $this->submallName = $submallName;
        return $this;
    }

    public function withProductDeliveryInfo(ProductDeliveryInfo $productDeliveryInfo)
    {
        $this->productDeliveryInfo = $productDeliveryInfo;
        return $this;
    }

    public function withOffers(array $offers)
    {
        foreach ($offers as $offer) {
            if (is_object($offer) && $offer instanceof Offer) {
                $offer->validRequired();
            }
        }
        $this->offerList = array_merge($this->offerList, $offers);
        return $this;
    }

    public function withLoyalties(array $loyalties)
    {
        foreach ($loyalties as $loyalty) {
            if (is_object($loyalty) && $loyalty instanceof Loyalty) {
                $loyalty->validRequired();
            }
        }
        $this->loyaltyList = array_merge($this->loyaltyList, $loyalties);
        return $this;
    }

    public function withMonthlyInstallment(array $monthlyInstallments)
    {
        foreach ($monthlyInstallments as $monthlyInstallment) {
            if (is_object($monthlyInstallment) && $monthlyInstallment instanceof MonthlyInstallment) {
                $monthlyInstallment->validRequired();
            }
        }
        $this->monthlyInstallmentList = array_merge($this->monthlyInstallmentList, $monthlyInstallments);
        return $this;
    }

    public function getOfferList()
    {
        return $this->offerList;
    }

    public function getLoyaltyList()
    {
        return $this->loyaltyList;
    }

    public function getShippingAddressList()
    {
        return $this->shippingAddressList;
    }
}
