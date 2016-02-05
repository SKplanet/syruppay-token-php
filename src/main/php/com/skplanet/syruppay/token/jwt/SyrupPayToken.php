<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-02
 * Time: 오후 5:24
 */

namespace com\skplanet\syruppay\token\jwt;

use com\skplanet\syruppay\token\claims\PayConfigurer;

class SyrupPayToken implements Token
{
    private $aud = "https://pay.syrup.co.kr";
    private $typ = "jose";
    private $iss;
    private $exp;
    private $iat;
    private $jti;
    private $nbf = 0;
    private $sub;

    /**
     * @var com\skplanet\syruppay\token\claims\MerchantUserConfigurer
     */
    private $loginInfo;
    /**
     * @var com\skplanet\syruppay\token\claims\PayConfigurer
     */
    private $transactionInfo;
    /**
     * @var com\skplanet\syruppay\token\claims\MapToSyrupPayUserConfigurer
     */
    private $userInfoMapper;
    /**
     * @var com\skplanet\syruppay\token\claims\OrderConfigurer
     */
    private $checkoutInfo;

    public function getCheckoutInfo()
    {
        return $this->checkoutInfo;
    }

    public function isValidInTime()
    {
        $current = time();
        return ($this->nbf <= 0 || $current > $this->nbf) && $current < $this->exp;
    }

    public function getAud()
    {
        return $this->aud;
    }

    public function setAud($aud)
    {
        $this->aud = $aud;
        return $this;
    }

    public function getTyp()
    {
        return $this->typ;
    }

    public function setTyp($typ)
    {
        $this->typ = $typ;
        return $this;
    }

    public function getIss()
    {
        return $this->iss;
    }

    public function setIss($iss)
    {
        $this->iss = $iss;
        return $this;
    }

    public function getExp()
    {
        return $this->exp;
    }

    public function setExp($exp)
    {
        $this->exp = $exp;
        return $this;
    }

    public function getIat()
    {
        return $this->iat;
    }

    public function setIat($iat)
    {
        $this->iat = $iat;
    }

    public function getJti()
    {
        return $this->jti;
    }

    public function setJti($jti)
    {
        $this->jti = $jti;
        return $this;
    }

    public function getNbf()
    {
        return isset($this->nbf) ? $this->nbf : 0;
    }

    public function setNbf($nbf)
    {
        $this->nbf = $nbf;
        return $this;
    }

    public function getSub()
    {
        return $this->sub;
    }

    public function setSub($sub)
    {
        $this->sub = $sub;
        return $this;
    }

    public function getLoginInfo()
    {
        return $this->loginInfo;
    }

    public function setLoginInfo($loginInfo)
    {
        $this->loginInfo = $loginInfo;
        return $this;
    }

    public function getTransactionInfo()
    {
        if (!isset($this->transactionInfo)) {
            $this->transactionInfo = new PayConfigurer();
        }
        return $this->transactionInfo;
    }

    public function setTransactionInfo($transactionInfo)
    {
        $this->transactionInfo = $transactionInfo;
        return $this;
    }

    public function getUserInfoMapper()
    {
        return $this->userInfoMapper;
    }

    public function setUserInfoMapper($userInfoMapper)
    {
        $this->userInfoMapper = $userInfoMapper;
        return $this;
    }

    public function getLineInfo()
    {
        return $this->lineInfo;
    }

    public function setLineInfo($lineInfo)
    {
        $this->lineInfo = $lineInfo;
        return $this;
    }
}
