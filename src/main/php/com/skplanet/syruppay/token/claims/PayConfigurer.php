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

class PayConfigurer extends AbstractTokenConfigurer
{
    protected $mctTransAuthId;
    protected $cashReceiptDisplay;
    protected $mctDefinedValue;
    /**
     * @var com\skplanet\syruppay\token\claims\PaymentInformationBySeller
     */
    protected $paymentInfo;
    /**
     * @var com\skplanet\syruppay\token\claims\PaymentRestriction
     */
    protected $paymentRestrictions;

    function __construct()
    {
        $this->paymentInfo = new PaymentInformationBySeller();
        $this->paymentRestrictions = new PaymentRestriction();
    }

    public static function isValidCountryAlpha2Code($code)
    {
        return in_array(strpos($code, ":") ? substr($code, strtoupper(strpos($code, ":") + 1)) : strtoupper($code), Locale::getISOCountries());
    }

    public static function isValidLanguageCode($code)
    {
        return in_array(Locale::getISOLanguages(), $code);
    }

    public function getMerchantTransactionAuthenticatedId()
    {
        return $this->mctTransAuthId;
    }

    public function getPaymentInfo()
    {
        return $this->paymentInfo;
    }

    public function getPaymentRestrictions()
    {
        return $this->paymentRestrictions;
    }

    public function withOrderIdOfMerchant($orderId)
    {
        $this->mctTransAuthId = $orderId;
        return $this;
    }

    public function withCashReceiptDisplay($cashReceiptDisplay)
    {
        if (!in_array(strtoupper($cashReceiptDisplay), CashReceiptDisplay::getCashReceiptDisplays()))
            throw new \InvalidArgumentException("cashReceiptDisplay should be one of 'YES' or 'NO'");

        $this->cashReceiptDisplay = $cashReceiptDisplay;
        return $this;
    }

    public function withMerchantDefinedValue($merchantDefinedValue)
    {
        $this->mctDefinedValue = $merchantDefinedValue;
        return $this;
    }

    public function getMerchantDefinedValue()
    {
        return $this->mctDefinedValue;
    }

    public function withProductTitle($productTile)
    {
        $this->paymentInfo->setProductTitle($productTile);
        return $this;
    }

    public function withProductUrls($productUrls)
    {
        if (is_string($productUrls)) {
            $productUrls = array($productUrls);
        }

        foreach ($productUrls as $productUrl) {
            if (!($this->startsWith($productUrl, "http") ||
                $this->startsWith($productUrl, "https"))
            ) {
                throw new \InvalidArgumentException("product details should be contained http or https urls. check your input!");
            }
        }

        $this->paymentInfo->setProductUrls($productUrls);
        return $this;
    }

    private function startsWith($haystack, $needle)
    {
        return isset($needle) && strrpos($haystack, $needle, 0) !== FALSE;
    }

    public function withLanguageForDisplay($lang)
    {
        $this->paymentInfo->setLang($lang);
        return $this;
    }

    public function withCurrency($currency)
    {
        $this->paymentInfo->setCurrencyCode($currency);
        return $this;
    }

    public function withShippingAddress(ShippingAddress $shippingAddress)
    {
        $this->paymentInfo->setShippingAddress($shippingAddress->mapToStringForFds());
        return $this;
    }

    public function withAmount($paymentAmount)
    {
        if ($paymentAmount <= 0) {
            throw new \InvalidArgumentException("Cannot be smaller than 0. Check yours input value : " . $paymentAmount);
        }

        $this->paymentInfo->setPaymentAmt($paymentAmount);
        return $this;
    }

    public function withDeliveryPhoneNumber($deliveryPhoneNumber)
    {
        $this->paymentInfo->setDeliveryPhoneNumber($deliveryPhoneNumber);
        return $this;
    }

    public function withDeliveryName($deliveryName)
    {
        $this->paymentInfo->setDeliveryName($deliveryName);
        return $this;
    }

    public function withBeAbleToExchangeToCash($exchangeable)
    {
        $this->paymentInfo->setExchangeable($exchangeable);
        return $this;
    }

    public function withInstallmentPerCardInformation($cards)
    {
        $cardInfoList = array();
        if (!is_array($cards) && $cards instanceof CardInstallmentInformation) {
            $cardInfoList[] = $cards;
        } else if (is_array($cards)) {
            $cardInfoList = $cards;
        }

        $this->paymentInfo->setCardInfoList($cardInfoList);
        return $this;
    }

    public function withBankInfo($bankCodes)
    {
        $bankInfoList = array();
        if (!is_array($bankCodes) instanceof Bank)
        {
            $bankInfoList[] = $bankCodes;
        }
        else
        {
            $bankInfoList = $bankCodes;
        }

        $this->paymentInfo->setBankInfoList($bankInfoList);
        return $this;
    }

    public function withPayableRuleWithCard($payableLocaleRule)
    {
        $this->paymentRestrictions->setCardIssuerRegion($payableLocaleRule);
        return $this;
    }

    public function withRestrictionPaymentType($paymentType)
    {
        $this->paymentRestrictions->setPaymentType($paymentType);
        return $this;
    }

    public function withRestrictionUserType($matchedUser)
    {
        if (MatchedUser::CI_MATCHED_ONLY != $matchedUser)
        {
            throw new \InvalidArgumentException("matchedUser should be 'CI_MATCHED_ONLY");
        }

        $this->paymentRestrictions->setMatchedUser($matchedUser);
        return $this;
    }

    function claimName()
    {
        return "transactionInfo";
    }

    function validRequired()
    {
        $productTitle = $this->paymentInfo->getProductTitle();
        $lang = $this->paymentInfo->getLang();
        $currencyCode = $this->paymentInfo->getCurrencyCode();
        $paymentAmt = $this->paymentInfo->getPaymentAmt();

        if (!isset($this->mctTransAuthId) || !isset($productTitle) ||
            !isset($lang) || !isset($currencyCode) ||
            !isset($paymentAmt) || $paymentAmt <= 0
        ) {
            throw new \InvalidArgumentException("some of required fields is null or wrong. " .
                "you should set orderIdOfMerchant : " . $this->mctTransAuthId .
                ",  productTitle : " . $productTitle . ",  languageForDisplay : " . $lang .
                ",  currency : " . $currencyCode . ",  amount : " . $paymentAmt);
        }

        if (strlen($this->mctTransAuthId) > 40) {
            throw new \InvalidArgumentException("order id of merchant couldn't be longer than 40. but yours is " . strlen($this->mctTransAuthId));
        }
    }
}

class Language
{
    const KO = "KO";
    const EN = "EN";
}

class Currency
{
    const KRW = "KRW";
    const USD = "USD";
}

class PayableLocaleRule
{
    const ONLY_ALLOWED_KOR = "ALLOWED:KOR";
    const ONLY_NOT_ALLOWED_KOR = "NOT_ALLOWED:KOR";
    const ONLY_ALLOWED_USA = "ALLOWED:USA";
    const ONLY_NOT_ALLOWED_USA = "NOT_ALLOWED:USA";

    public static function getPayableLocaleRules()
    {
        return array(ONLY_ALLOWED_KOR, ONLY_NOT_ALLOWED_KOR, ONLY_ALLOWED_USA, ONLY_NOT_ALLOWED_USA);
    }
}

class MatchedUser
{
    const CI_MATCHED_ONLY = "CI_MATCHED_ONLY";
}

class DeliveryRestriction
{
    const NOT_FAR_AWAY = 'NOT_FAR_AWAY';
    const FAR_AWAY = 'FAR_AWAY';
    const FAR_FAR_AWAY = 'FAR_FAR_AWAY';
}

class CashReceiptDisplay
{
    const YES = "YES";
    const NO = "NO";
    const DELEGATE_ADMIN = "DELEGATE_ADMIN";

    public static function getCashReceiptDisplays()
    {
        return array(CashReceiptDisplay::YES, CashReceiptDisplay::NO, CashReceiptDisplay::DELEGATE_ADMIN);
    }
}

class ShippingAddress extends PropertyMapper
{
    protected $id;
    protected $userActionCode;
    protected $name;
    protected $countryCode;
    protected $zipCode;
    protected $mainAddress;
    protected $detailAddress;
    protected $city;
    protected $state;
    protected $recipientName;
    protected $recipientPhoneNumber;
    protected $deliveryRestriction;
    protected $defaultDeliveryCost;
    protected $additionalDeliveryCost;
    protected $orderApplied;

    public function __construct()
    {
        $argNumbers = func_num_args();
        if ($argNumbers == 0) {
            return;
        } else if ($argNumbers == 6) {
            $args = func_get_args();
            $this->zipCode = $args[0];
            $this->mainAddress = $args[1];
            $this->detailAddress = $args[2];
            $this->city = $args[3];
            $this->state = $args[4];
            $this->countryCode = $this->setCountryCode($args[5])->getCountryCode();
        } else {
            throw new \InvalidArgumentException("usage : new ShippingAddress(zipCode, mainAddress, detailAddress, city, state, countryCode)");
        }
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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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

    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public function setCountryCode($countryCode)
    {
        if (!PayConfigurer::isValidCountryAlpha2Code($countryCode)) {
            throw new \InvalidArgumentException("countryCode should meet the specifications of ISO-3166 Alpha2(as KR, US) except prefix like a2. yours : " . $this->countryCode);
        }
        $this->countryCode = strtolower($countryCode);
        return $this;
    }

    public function getZipCode()
    {
        return $this->zipCode;
    }

    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getMainAddress()
    {
        return $this->mainAddress;
    }

    public function setMainAddress($mainAddress)
    {
        $this->mainAddress = $mainAddress;
        return $this;
    }

    public function getDetailAddress()
    {
        return $this->detailAddress;
    }

    public function setDetailAddress($detailAddress)
    {
        $this->detailAddress = $detailAddress;
        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    public function getRecipientName()
    {
        return $this->recipientName;
    }

    public function setRecipientName($recipientName)
    {
        $this->recipientName = $recipientName;
        return $this;
    }

    public function getRecipientPhoneNumber()
    {
        return $this->recipientPhoneNumber;
    }

    public function setRecipientPhoneNumber($recipientPhoneNumber)
    {
        if (!isset($recipientPhoneNumber) || !preg_match_all('/^([0-9]*)$/', $recipientPhoneNumber, $match)) {
            throw new \InvalidArgumentException("phone number should be contained numbers. remove characters as '-'. yours : " . $recipientPhoneNumber);
        }

        $this->recipientPhoneNumber = $recipientPhoneNumber;
        return $this;
    }

    public function getDeliveryRestriction()
    {
        return $this->deliveryRestriction;
    }

    public function setDeliveryRestriction($deliveryRestriction)
    {
        $this->deliveryRestriction = $deliveryRestriction;
        return $this;
    }

    public function getDefaultDeliveryCost()
    {
        return $this->defaultDeliveryCost;
    }

    public function setDefaultDeliveryCost($defaultDeliveryCost)
    {
        $this->defaultDeliveryCost = $defaultDeliveryCost;
        return $this;
    }

    public function getAdditionalDeliveryCost()
    {
        return $this->additionalDeliveryCost;
    }

    public function setAdditionalDeliveryCost($additionalDeliveryCost)
    {
        $this->additionalDeliveryCost = $additionalDeliveryCost;
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

    public function mapToStringForFds()
    {
        return $this->countryCode . "|" . $this->zipCode . "|" . $this->mainAddress . "|" . $this->detailAddress . "|" . $this->city . "|" . $this->state . "|";
    }

    public function validRequiredToCheckout()
    {
        if (!isset($this->id) || !isset($this->name) ||
            !isset($this->countryCode) || !isset($this->zipCode) ||
            !isset($this->mainAddress) || !isset($this->detailAddress) ||
            !isset($this->recipientName) || !isset($this->recipientPhoneNumber)
        ) {
            throw new \InvalidArgumentException("ShippingAddress object to checkout couldn't be with null fields. id : " . $this->id . ", name : " . $this->name . ", countryCode : " . $this->countryCode . ", zipCode : " . $this->zipCode . ", mainAddress : " . $this->mainAddress . ", detailAddress : " . $this->detailAddress . ", recipientName : " . $this->recipientName . ", recipientPhoneNumber : " . $this->recipientPhoneNumber);
        }

        if (!PayConfigurer::isValidCountryAlpha2Code($this->countryCode)) {
            throw new \InvalidArgumentException("countryCode should meet the specifications of ISO-3166 Alpha2(as KR, US) except prefix like a2. yours : " . $this->countryCode);
        }

        if ($this->defaultDeliveryCost <= 0) {
            throw new \InvalidArgumentException("defaultDeliveryCost field should be bigger than 0. yours : " . $this->defaultDeliveryCost);
        }
    }
}

class CardInstallmentInformation extends PropertyMapper
{
    protected $cardCode;
    protected $monthlyInstallmentInfo;

    public function __construct()
    {
        $argNumbers = func_num_args();
        if ($argNumbers == 0) {
            return;
        } else if ($argNumbers == 2) {
            $args = func_get_args();
            $this->cardCode = $args[0];
            $this->monthlyInstallmentInfo = $args[1];
        } else {
            throw new \InvalidArgumentException("usage : new CardInstallmentInformation(cardCode, monthlyInstallmentInfo)");
        }
    }

    public function getCardCode()
    {
        return $this->cardCode;
    }

    public function getMonthlyInstallmentInfo()
    {
        return $this->monthlyInstallmentInfo;
    }
}

class PaymentInformationBySeller extends PropertyMapper
{
    /**
     * @var com\skplanet\syruppay\token\claims\CardInstallmentInformation
     */
    protected $cardInfoList = array();
    protected $productTitle;
    protected $productUrls = array();
    protected $lang = "KO";
    protected $currencyCode = "KRW";
    protected $paymentAmt;
    protected $shippingAddress;
    protected $deliveryPhoneNumber;
    protected $deliveryName;
    protected $isExchangeable;
    protected $bankInfoList = array();

    public function getProductTitle()
    {
        return $this->productTitle;
    }

    public function setProductTitle($productTitle)
    {
        $this->productTitle = $productTitle;
    }

    public function getProductUrls()
    {
        return $this->productUrls;
    }

    public function setProductUrls(array $productUrls)
    {
        $this->productUrls = array_merge($this->productUrls, $productUrls);
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode($currency)
    {
        $this->currencyCode = $currency;
    }

    public function getPaymentAmt()
    {
        return $this->paymentAmt;
    }

    public function setPaymentAmt($paymentAmt)
    {
        $this->paymentAmt = $paymentAmt;
    }

    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getDeliveryPhoneNumber()
    {
        return $this->deliveryPhoneNumber;
    }

    public function setDeliveryPhoneNumber($deliveryPhoneNumber)
    {
        $this->deliveryPhoneNumber = $deliveryPhoneNumber;
    }

    public function getDeliveryName()
    {
        return $this->deliveryName;
    }

    public function setDeliveryName($deliveryName)
    {
        $this->deliveryName = $deliveryName;
    }

    public function isExchangeable()
    {
        return $this->isExchangeable;
    }

    public function setExchangeable($isExchangeable)
    {
        $this->isExchangeable = $isExchangeable;
    }

    public function getCardInfoList()
    {
        return $this->cardInfoList;
    }

    public function setCardInfoList(array $cardInfoList)
    {
        $this->cardInfoList = array_merge($this->cardInfoList, $cardInfoList);
    }

    public function getBankInfoList()
    {
        return $this->bankInfoList;
    }

    public function setBankInfoList(array $bankInfoList)
    {
        $this->bankInfoList = array_merge($this->bankInfoList, $bankInfoList);
    }
}

class PaymentRestriction extends PropertyMapper
{
    protected $cardIssuerRegion = "ALLOWED:KOR";
    protected $paymentType;
    protected $matchedUser;

    public function getCardIssuerRegion()
    {
        return $this->cardIssuerRegion;
    }

    public function setCardIssuerRegion($cardIssuerRegion)
    {
        $this->cardIssuerRegion = $cardIssuerRegion;
    }

    public function getPayableLocaleRule()
    {
        if (in_array(strtoupper($this->cardInfoList), PayableLocaleRule::getPayableLocaleRules())) {
            return $this->cardInfoList;
        }

        throw new \InvalidArgumentException("cardIssuerRegion of this object is not matched with PaymentRestriction enumeration. check this : " . $this->cardIssuerRegion);
    }

    public function getPaymentType()
    {
        return $this->paymentType;
    }

    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }

    public function getMatchedUser()
    {
        return $this->matchedUser;
    }

    public function setMatchedUser($matchedUser)
    {
        $this->matchedUser = $matchedUser;
    }
}

class Bank extends PropertyMapper
{
    protected $bankCode;

    public function __construct()
    {
        if (func_num_args() == 1)
        {
            $bankCodes = func_get_arg(0);
            if (is_array($bankCodes))
            {
                foreach ($bankCodes as $bankCode)
                {
                    if (isset($this->bankCode))
                        $this->bankCode .= ":";
                    $this->bankCode .= $bankCode;
                }
            }
            else
            {
                throw new \InvalidArgumentException("bankCode is array type. ex) array('bankCode', 'bankCode')");
            }
        }
    }

    public function getBankCode()
    {
        return $this->bankCode;
    }
}
