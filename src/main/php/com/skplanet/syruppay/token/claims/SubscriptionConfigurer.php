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


use com\skplanet\syruppay\token\PropertyMapper;

class SubscriptionConfigurer extends AbstractTokenConfigurer
{
    protected $mctSubscriptRequestId;
    protected $autoPaymentId;
    /**
     * @var com\skplanet\syruppay\token\claims\SubscriptionPlan
     */
    protected $plan;
    /**
     * @var com\skplanet\syruppay\token\claims\RegistrationRestrictions
     */
    protected $registrationRestrictions;

    function __construct()
    {
        $this->plan = new SubscriptionPlan();
        $this->registrationRestrictions = new RegistrationRestrictions();
    }

    public function withMerchantSubscriptionId($mctSubscriptRequestId)
    {
        $this->mctSubscriptRequestId = $mctSubscriptRequestId;
        return $this;
    }

    public function withAutoPaymentId($autoPaymentId)
    {
        $this->autoPaymentId = $autoPaymentId;
        return $this;
    }

    public function withInterval($interval)
    {
        if (!in_array(strtoupper($interval), Interval::getInverals()))
        {
            throw new \InvalidArgumentException("interval value should be one of Interval constants. input interval is ".$interval);
        }

        $this->plan->setInterval($interval);
        return $this;
    }

    public function withServiceName($name)
    {
        $this->plan->setName($name);
        return $this;
    }

    public function withRestrictionUserType($matchedUser)
    {
        if (MatchedUser::CI_MATCHED_ONLY != $matchedUser)
        {
            throw new \InvalidArgumentException("matchedUser should be 'CI_MATCHED_ONLY");
        }

        $this->registrationRestrictions->setMatchedUser($matchedUser);
        return $this;
    }

    public function getPlan()
    {
        return $this->plan;
    }

    public function getRegistrationRestrictions()
    {
        return $this->registrationRestrictions;
    }

    function claimName()
    {
        return "subscription";
    }

    function validRequired()
    {
    }
}

class Interval
{
    const ONDEMAND = "ONDEMAND";
    const MONTHLY = "MONTHLY";
    const WEEKLY = "WEEKLY";
    const BIWEEKLY = "BIWEEKLY";

    static function getInverals()
    {
        return array(Interval::ONDEMAND, Interval::MONTHLY, Interval::WEEKLY, Interval::BIWEEKLY);
    }
}

class SubscriptionPlan extends PropertyMapper
{
    protected $interval;
    protected $name;

    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}

class RegistrationRestrictions extends PropertyMapper
{
    protected $matchedUser;

    public function setMatchedUser($matchedUser)
    {
        $this->matchedUser = $matchedUser;
    }

    public function getMatchedUser()
    {
        return $this->matchedUser;
    }
}

