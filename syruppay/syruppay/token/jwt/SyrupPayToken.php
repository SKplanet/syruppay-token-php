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

class syruppay_token_jwt_SyrupPayToken implements syruppay_token_jwt_Token
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
     * @var syruppay\token\claims\syruppay_token_claims_MerchantUserConfigurer
     */
    private $loginInfo;
    /**
     * @var syruppay\token\claims\syruppay_token_claims_PayConfigurer
     */
    private $transactionInfo;
    /**
     * @var syruppay\token\claims\syruppay_token_claims_MapToSyrupPayUserConfigurer
     */
    private $userInfoMapper;
    /**
     * @var syruppay\token\claims\syruppay_token_claims_OrderConfigurer
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
            $this->transactionInfo = new syruppay_token_claims_PayConfigurer();
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
