<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-06
 * Time: 오전 1:10
 */

namespace com\skplanet\syruppay\token\claims\elements;


use com\skplanet\syruppay\token\utils\ClassPropertyUtils;

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
     * @var com\skplanet\syruppay\token\claims\elements\AdditionalDiscount
     */
    private $additionalDiscount;
    private $error;
    private $exclusiveGroupId;
    private $exclusiveGroupName;
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

    public function setIdBy($loyaltyId)
    {
        $this->id = $loyaltyId;
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
            throw new \InvalidArgumentException("Loyalty object couldn't be with null fields id : " . $this->id . ", name : " . $this->name . ", subscriberId : " . $this->subscriberId);
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

class LoyaltyId
{
    const POINT_OF_11ST = "www.11st.co.kr:point";
    const MILEAGE_OF_11ST = "www.11st.co.kr:mileage";
    const T_MEMBERSHIP = "www.sktmemebership.co.kr";
    const OK_CASHBAG = "www.okcashbag.com";
}
