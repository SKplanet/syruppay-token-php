<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-06
 * Time: 오전 1:05
 */

namespace com\skplanet\syruppay\token\claims\elements;


use com\skplanet\syruppay\token\utils\ClassPropertyUtils;

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

class OfferType
{
    const DELIVERY_COUPON = 'DELIVERY_COUPON';
}
