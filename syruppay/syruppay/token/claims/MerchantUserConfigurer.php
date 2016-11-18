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


class MerchantUserConfigurer extends AbstractTokenConfigurer
{
    protected $mctUserId;
    protected $extraUserId;
    protected $SSOCredential;
    protected $deviceIdentifier;
    protected $SSOPolicy;

    public function getMctUserId()
    {
        return $this->mctUserId;
    }

    public function getExtraUserId()
    {
        return $this->extraUserId;
    }

    public function getSsoCredential()
    {
        return $this->SSOCredential;
    }

    public function getSsoPolicy()
    {
        return $this->SSOPolicy;
    }

    public function withSsoCredential($ssoCredential)
    {
        $this->SSOCredential = $ssoCredential;
        return $this;
    }

    public function withMerchantUserId($merchantUserId)
    {
        $this->mctUserId = $merchantUserId;
        return $this;
    }

    public function withExtraMerchantUserId($extraMerchantUserId)
    {
        $this->extraUserId = $extraMerchantUserId;
        return $this;
    }

    public function withDeviceIdentifier($deviceIdentifier)
    {
        $this->deviceIdentifier = $deviceIdentifier;
        return $this;
    }

    public function isNotApplicableSso() {
        $this->SSOPolicy = "NOT_APPLICABLE";
        return $this;
    }

    function claimName()
    {
        return "loginInfo";
    }

    function validRequired()
    {
        if (!isset($this->mctUserId))
        {
            throw new \InvalidArgumentException("when you try to login or sign up, merchant user id couldn't be null. you should set merchant user id  by SyrupPayTokenHandler->login()->withMerchantUserId(String) or SyrupPayTokenHandler->signup()->withMerchantUserId(String)");
        }
    }
}
