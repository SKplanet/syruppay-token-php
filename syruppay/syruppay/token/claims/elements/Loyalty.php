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

class syruppay_token_claims_elements_Loyalty extends syruppay_token_PropertyMapper implements syruppay_token_claims_elements_Element
{
    protected $id;
    protected $userActionCode;
    protected $name;
    protected $subscriberId;
    protected $balance;
    protected $maxApplicableAmt;
    protected $initialAppliedAmt;
    protected $orderApplied;
    /**
     * @var syruppay\token\claims\elements\syruppay_token_claims_elements_AdditionalDiscount
     */
    protected $additionalDiscount;
    /**
     * @var syruppay\token\claims\elements\syruppay_token_claims_elements_Error
     */
    protected $error;
    protected $exclusiveGroupId;
    protected $exclusiveGroupName;
    protected $applicableForNotMatchedUser;

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
            throw new InvalidArgumentException("balance field should be bigger than 0. yours balance is : " . $balance);
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
            throw new InvalidArgumentException("maxApplicableAmt field should be bigger than 0. yours maxApplicableAmt is : " . $maxApplicableAmt);
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

    public function setAdditionalDiscount(syruppay_token_claims_elements_AdditionalDiscount $additionalDiscount)
    {
        $this->additionalDiscount = $additionalDiscount;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError(syruppay_token_claims_elements_Error $error)
    {
        $this->error = $error;
        return $this;
    }

    public function validRequired()
    {
        if (!isset($this->id) || !isset($this->name) || !isset($this->subscriberId)) {
            throw new InvalidArgumentException("Loyalty object couldn't be with null fields id : " . $this->id . ", name : " . $this->name . ", subscriberId : " . $this->subscriberId);
        }

        if (isset($this->additionalDiscount)) {
            $this->additionalDiscount->validRequired();
        }

        if (isset($this->error)) {
            $this->error->validRequired();
        }

        if ($this->balance <= 0) {
            throw new InvalidArgumentException("balance field should be bigger than 0. yours balance is : " . $this->balance);
        }
        if ($this->maxApplicableAmt <= 0) {
            throw new InvalidArgumentException("maxApplicableAmt field should be bigger than 0. yours maxApplicableAmt is : " . $this->maxApplicableAmt);
        }
    }
}
