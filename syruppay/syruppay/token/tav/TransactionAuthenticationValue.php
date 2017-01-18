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

class syruppay_token_tav_TransactionAuthenticationValue extends syruppay_token_PropertyMapper
{
    protected $cardToken;
    protected $mctTransAuthId;
    protected $ocTransAuthId;
    /**
     * @var syruppay\token\tav\syruppay_token_tav_PaymentAuthenticationDetail
     */
    protected $paymentAuthenticationDetail;

    public function getCardToken()
    {
        return $this->cardToken;
    }

    public function getOrderIdOfMerchant()
    {
        return $this->mctTransAuthId;
    }

    public function getTransactionIdOfOneClick()
    {
        return $this->ocTransAuthId;
    }

    public function getPaymentAuthenticationDetail()
    {
        return $this->paymentAuthenticationDetail;
    }

    public function getChecksumBy($key)
    {
        return $this->getChecksum($key);
    }

    private function getChecksum($key)
    {
        $json = $this->paymentAuthenticationDetail->toJson();
        $result = hash_hmac('sha256', implode('', array($this->cardToken + $this->mctTransAuthId + $this->ocTransAuthId + $json)), $key, true);
        return syruppay_jose_util_Base64UrlSafeEncoder::encode($result);
    }

    public function isValidBy($key, $checksum)
    {
        return strcmp($this->getChecksum($key), $checksum) == 0;
    }
}
