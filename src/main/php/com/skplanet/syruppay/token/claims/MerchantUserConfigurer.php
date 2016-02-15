<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-03
 * Time: 오후 2:24
 */

namespace com\skplanet\syruppay\token\claims;


class MerchantUserConfigurer extends AbstractTokenConfigurer
{
    protected $mctUserId;
    protected $extraUserId;
    protected $SSOCredential;
    protected $deviceIdentifier;

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
