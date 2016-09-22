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

namespace syruppay\token\claims;


use syruppay\token\claims\elements\Plan;
use syruppay\token\claims\elements\RegistrationRestrictions;
use syruppay\token\claims\value\Interval;
use syruppay\token\claims\value\MatchedUser;

class SubscriptionConfigurer extends AbstractTokenConfigurer
{
    protected $mctSubscriptRequestId;
    protected $autoPaymentId;
    /**
     * @var syruppay\token\claims\elements\Plan
     */
    protected $plan;
    /**
     * @var syruppay\token\claims\elements\RegistrationRestrictions
     */
    protected $registrationRestrictions;

    function __construct()
    {
        $this->plan = new Plan();
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
        if (MatchedUser::CI_MATCHED_ONLY != $matchedUser &&
        MatchedUser::FIRST_SIGNUP_IN_LIFETIME_ONLY != $matchedUser)
        {
            throw new \InvalidArgumentException("matchedUser should be 'CI_MATCHED_ONLY' or 'FIRST_SIGNUP_IN_LIFETIME_ONLY'");
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
