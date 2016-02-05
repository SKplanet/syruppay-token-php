<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-03
 * Time: 오후 7:57
 */

namespace com\skplanet\syruppay\token\claims;


use com\skplanet\syruppay\token\utils\ClassPropertyUtils;

class OrderConfigurer extends AbstractTokenConfigurer
{
    use ClassPropertyUtils;

    private $productPrice;
    private $submallName;
    private $privacyPolicyRequirements;
    private $mainShippingAddressSettingDisabled;
    /**
     * @var com\skplanet\syruppay\token\claims\ProductDeliveryInfo
     */
    private $productDeliveryInfo;
    /**
     * @var com\skplanet\syruppay\token\claims\Offer
     */
    private $offerList = array();
    /**
     * @var com\skplanet\syruppay\token\claims\Loyalty
     */
    private $loyaltyList = array();
    /**
     * @var com\skplanet\syruppay\token\claims\ShippingAddress
     */
    private $shippingAddressList = array();
    /**
     * @var com\skplanet\syruppay\token\claims\MonthlyInstallment
     */
    private $monthlyInstallmentList = array();

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

class LoyaltyId
{
    const POINT_OF_11ST = "www.11st.co.kr:point";
    const MILEAGE_OF_11ST = "www.11st.co.kr:mileage";
    const T_MEMBERSHIP = "www.sktmemebership.co.kr";
    const OK_CASHBAG = "www.okcashbag.com";
}

class ErrorType
{
    const MAINTENACE = 'MAINTENACE';
    const SYSTEM_ERR = 'SYSTEM_ERR';
}

class DeliveryRestriction
{
    const NOT_FAR_AWAY = 'NOT_FAR_AWAY';
    const FAR_AWAY = 'FAR_AWAY';
    const FAR_FAR_AWAY = 'FAR_FAR_AWAY';
}

class OfferType
{
    const DELIVERY_COUPON = 'DELIVERY_COUPON';
}

class AcceptType
{
    const CARD = 'CARD';
    const SYRUP_PAY_COUPON = 'const';
}

class DeliveryType
{
    const PREPAID = 'PREPAID';
    const FREE = 'FREE';
    const DIY = 'DIY';
    const QUICK = 'QUICK';
    const PAYMENT_ON_DELIVERY = 'PAYMENT_ON_DELIVERY';
}

interface Element
{
    function validRequired();
}

class Accept implements Element
{
    use ClassPropertyUtils;

    private $type;
    private $conditions = array();

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
        return $this;
    }

    function validRequired()
    {
        if (!isset($type)) {
            throw new \InvalidArgumentException("Accept object couldn't be with null fields.");
        }

        if (!isset($this->conditions) || empty($this->conditions)) {
            throw new \InvalidArgumentException("Conditions of Accept object couldn't be empty. you should contain with conditions of Accept object.");
        }
    }
}

class ProductDeliveryInfo implements Element
{
    use ClassPropertyUtils;

    private $deliveryType;
    private $deliveryName;
    private $defaultDeliveryCostApplied;
    private $additionalDeliveryCostApplied;
    private $shippingAddressDisplay;

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
        if (!$deliveryType('DeliveryType::' . $deliveryType)) {
            throw new \InvalidArgumentException("Parameter value must be DeliveryType's constants");
        }
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

class Offer implements Element
{
    use ClassPropertyUtils;

    private $id;
    private $userActionCode;
    private $type;
    private $name;
    private $amountOff;
    private $userSelectable;
    private $orderApplied;
    private $exclusiveGroupId;
    private $exclusiveGroupName;
    /**
     * @var com\skplanet\syruppay\token\claims\Accept
     */
    private $accepted = array();
    private $applicableForNotMatchedUser;

    public function isApplicableForNotMatchedUser()
    {
        return $this->applicableForNotMatchedUser;
    }

    public function setApplicableForNotMatchedUser($applicableForNotMatchedUser)
    {
        $this->applicableForNotMatchedUser = $applicableForNotMatchedUser;
        return $this;
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

    public function getExclusiveGroupId()
    {
        return $this->exclusiveGroupId;
    }

    public function setExclusiveGroupId($exclusiveGroupId)
    {
        $this->exclusiveGroupId = $exclusiveGroupId;
        return $this;
    }

    public function getExclusiveGroupName()
    {
        return $this->exclusiveGroupName;
    }

    public function setExclusiveGroupName($exclusiveGroupName)
    {
        $this->exclusiveGroupName = $exclusiveGroupName;
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

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
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

    public function getAmountOff()
    {
        return $this->amountOff;
    }

    public function setAmountOff($amountOff)
    {
        if ($amountOff <= 0) {
            throw new \InvalidArgumentException("amountOff should be bigger than 0. yours : " . $amountOff);
        }

        $this->amountOff = $amountOff;
        return $this;
    }

    public function isUserSelectable()
    {
        return $this->userSelectable;
    }

    public function setUserSelectable($userSelectable)
    {
        $this->userSelectable = $userSelectable;
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

    public function getAccepted()
    {
        return $this->accepted;
    }

    public function setAccepted(array $accepted)
    {
        $this->accepted = $accepted;
        return $this;
    }

    public function validRequired()
    {
        if (!isset($this->id) || !isset($this->name)) {
            throw new \InvalidArgumentException("Offer object couldn't be with null fields id : " . $this->id . ", name : " . $this->name);
        }
        if ($this->amountOff <= 0) {
            throw new \InvalidArgumentException("amountOff field should be bigger than 0. yours amountOff is : " . $this->amountOff);
        }
    }
}

class AdditionalDiscount implements Element
{
    use ClassPropertyUtils;

    private $percentOff;
    private $maxApplicableAmt;

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

class Error implements Element
{
    use ClassPropertyUtils;

    private $type;
    private $description;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function validRequired()
    {
        if (!isset($this->type) || !isset($this->description)) {
            throw new \InvalidArgumentException("Error object couldn't be with null fields type : " + type + ", description : " + description);
        }
    }
}

class Loyalty implements Element
{
    use ClassPropertyUtils;

    private $id;
    private $userActionCode;
    private $name;
    private $subscriberId;
    private $balance;
    private $maxApplicableAmt;
    private $initialAppliedAmt;
    private $orderApplied;
    /**
     * @var com\skplanet\syruppay\token\claims\AdditionalDiscount
     */
    private $additionalDiscount;
    private $error;
    private $exclusiveGroupId;
    private $exclusiveGroupName;
    private $applicableForNotMatchedUser;

    public function isApplicableForNotMatchedUser()
    {
        return applicableForNotMatchedUser;
    }

    public function setApplicableForNotMatchedUser($applicableForNotMatchedUser)
    {
        $this->applicableForNotMatchedUser = $applicableForNotMatchedUser;
        return $this;
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

    public function getExclusiveGroupId()
    {
        return $this->exclusiveGroupId;
    }

    public function setExclusiveGroupId($exclusiveGroupId)
    {
        $this->exclusiveGroupId = $exclusiveGroupId;
        return $this;
    }

    public function getExclusiveGroupName()
    {
        return $this->exclusiveGroupName;
    }

    public function setExclusiveGroupName($exclusiveGroupName)
    {
        $this->exclusiveGroupName = $exclusiveGroupName;
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

    public function setIdBy(LoyaltyId $loyaltyId)
    {
        $this->id = $loyaltyId->getUrn();
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

    public function getSubscriberId()
    {
        return $this->subscriberId;
    }

    public function setSubscriberId($subscriberId)
    {
        $this->subscriberId = $subscriberId;
        return $this;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function setBalance($balance)
    {
        if ($balance <= 0) {
            throw new IllegalArgumentException("balance field should be bigger than 0. yours balance is : " . $balance);
        }
        $this->balance = $balance;
        return $this;
    }

    public function getMaxApplicableAmt()
    {
        return $this->maxApplicableAmt;
    }

    public function setMaxApplicableAmt($maxApplicableAmt)
    {
        if ($maxApplicableAmt <= 0) {
            throw new IllegalArgumentException("maxApplicableAmt field should be bigger than 0. yours maxApplicableAmt is : " . $maxApplicableAmt);
        }
        $this->maxApplicableAmt = $maxApplicableAmt;
        return $this;
    }

    public function getInitialAppliedAmt()
    {
        return $this->initialAppliedAmt;
    }

    public function setInitialAppliedAmt($initialAppliedAmt)
    {
        $this->initialAppliedAmt = $initialAppliedAmt;
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

    public function getAdditionalDiscount()
    {
        return $this->additionalDiscount;
    }

    public function setAdditionalDiscount(AdditionalDiscount $additionalDiscount)
    {
        $this->additionalDiscount = $additionalDiscount;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError(Error $error)
    {
        $this->error = $error;
        return $this;
    }

    public function validRequired()
    {
        if (!isset($this->id) || !isset($this->name) || !isset($this->subscriberId)) {
            throw new IllegalArgumentException("Loyalty object couldn't be with null fields id : " . $this->id . ", name : " . $this->name . ", subscriberId : " . $this->subscriberId);
        }

        if (isset($this->additionalDiscount)) {
            $this->additionalDiscount->validRequired();
        }

        if (isset($this->error)) {
            $this->error->validRequired();
        }

        if ($this->balance <= 0) {
            throw new \InvalidArgumentException("balance field should be bigger than 0. yours balance is : " . $this->balance);
        }
        if ($this->maxApplicableAmt <= 0) {
            throw new \InvalidArgumentException("maxApplicableAmt field should be bigger than 0. yours maxApplicableAmt is : " . $this->maxApplicableAmt);
        }
    }
}

class MonthlyInstallment implements Element
{
    use ClassPropertyUtils;

    private $cardCode;
    private $conditions = array();

    public function getCardCode()
    {
        return $this->cardCode;
    }

    public function setCardCode($cardCode)
    {
        $this->cardCode = $cardCode;
        return this;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function addConditionIncludeMax($min, $includeMin, $max, $includeMax, $monthlyInstallmentInfo)
    {
        $m = array();
        $m["paymentAmtRange"] = ($includeMin ? "[" : "(") . $min . "-" . $max . ($includeMax ? "]" : ")");
        $m["monthlyInstallmentInfo"] = $monthlyInstallmentInfo;
        $this->conditions[] = $m;
        return $this;
    }

    public function addCondition($min, $includeMin, $monthlyInstallmentInfo)
    {
        $m = array();
        $m["paymentAmtRange"] = ($includeMin ? "[" : "(") . $min . "-]";
        $m["monthlyInstallmentInfo"] = $monthlyInstallmentInfo;
        $this->conditions[] = $m;
        return $this;
    }

    public function validRequired()
    {
        if (!isset($this->cardCode)) {
            throw new \InvalidArgumentException("MonthlyInstallment object couldn't be with null fields cardCode is null");
        }

        if (!isset($this->conditions) || empty($this->conditions)) {
            throw new \InvalidArgumentException("Conditions of MonthlyInstallment object couldn't be empty. you should contain with conditions of MonthlyInstallment object by addCondition method.");
        }
    }
}
