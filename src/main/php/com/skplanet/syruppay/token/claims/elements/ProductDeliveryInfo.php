<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-06
 * Time: 오전 1:08
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
