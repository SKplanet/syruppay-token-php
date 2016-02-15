<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-05
 * Time: 오전 10:49
 */

namespace com\skplanet\syruppay\token\tav;


use com\skplanet\jose\util\Base64UrlSafeEncoder;
use com\skplanet\syruppay\token\PropertyMapper;

class TransactionAuthenticationValue extends PropertyMapper
{
    protected $cardToken;
    protected $mctTransAuthId;
    protected $ocTransAuthId;
    /**
     * @var com\skplanet\syruppay\token\tav\PaymentAuthenticationDetail
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
        return Base64UrlSafeEncoder::encode($result);
    }

    public function isValidBy($key, $checksum)
    {
        return strcmp($this->getChecksum($key), $checksum) == 0;
        return Base64 . encodeBase64URLSafeString(mac . doFinal()) . equals(checksum);
    }
}

class PaymentAuthenticationDetail extends PropertyMapper
{
    protected $payMethod;
    protected $payAmount;
    protected $offerAmount;
    protected $loyaltyAmount;
    protected $payInstallment;
    protected $payCurrency;
    protected $payFinanceCode;
    protected $isCardPointApplied;

    public function getPayMethod()
    {
        return $this->payMethod;
    }

    public function setPayMethod($payMethod)
    {
        $this->payMethod = $payMethod;
    }

    public function getPayAmount()
    {
        return $this->payAmount;
    }

    public function setPayAmount($payAmount)
    {
        $this->payAmount = $payAmount;
    }

    public function getOfferAmount()
    {
        return $this->offerAmount;
    }

    public function setOfferAmount($offerAmount)
    {
        $this->offerAmount = $offerAmount;
    }

    public function getLoyaltyAmount()
    {
        return $this->loyaltyAmount;
    }

    public function setLoyaltyAmount($loyaltyAmount)
    {
        $this->loyaltyAmount = $loyaltyAmount;
    }

    public function getPayInstallment()
    {
        return $this->payInstallment;
    }

    public function setPayInstallment($payInstallment)
    {
        $this->payInstallment = $payInstallment;
    }

    public function getPayCurrency()
    {
        return $this->payCurrency;
    }

    public function setPayCurrency($payCurrency)
    {
        $this->payCurrency = $payCurrency;
    }

    public function getPayFinanceCode()
    {
        return $this->payFinanceCode;
    }

    public function setPayFinanceCode($payFinanceCode)
    {
        $this->payFinanceCode = $payFinanceCode;
    }

    public function getIsCardPointApplied()
    {
        return $this->isCardPointApplied;
    }

    public function setIsCardPointApplied($isCardPointApplied)
    {
        $this->isCardPointApplied = $isCardPointApplied;
    }

    public function toJson()
    {
        $thisArray = $this->__toArray();
        return json_encode($thisArray);
    }
}
