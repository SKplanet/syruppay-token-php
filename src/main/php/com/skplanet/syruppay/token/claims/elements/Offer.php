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

class Offer extends PropertyMapper implements Element
{
    protected $id;
    protected $userActionCode;
    protected $type;
    protected $name;
    protected $amountOff;
    protected $userSelectable;
    protected $orderApplied;
    protected $applicableForNotMatchedUser;
    protected $exclusiveGroupId;
    protected $exclusiveGroupName;
    /**
     * @var com\skplanet\syruppay\token\claims\elements\Accept
     */
    protected $accepted = array();

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
