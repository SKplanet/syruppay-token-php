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

use syruppay\token\claims\elements\CardInstallmentInformation;
use syruppay\token\claims\elements\PaymentInformationBySeller;
use syruppay\token\claims\elements\PaymentRestriction;
use syruppay\token\claims\elements\ShippingAddress;
use syruppay\token\claims\value\CashReceiptDisplay;
use syruppay\token\claims\value\MatchedUser;

class PayConfigurer extends AbstractTokenConfigurer
{
    protected $mctTransAuthId;
    protected $cashReceiptDisplay;
    protected $mctDefinedValue;
    /**
     * @var syruppay\token\claims\elements\PaymentInformationBySeller
     */
    protected $paymentInfo;
    /**
     * @var syruppay\token\claims\elements\PaymentRestriction
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

    public function withDeliveryType($deliveryType)
    {
        $this->paymentInfo->setDeliveryType($deliveryType);
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

    public function withBankInfos(array $bankInfos)
    {
        $this->paymentInfo->setBankInfoList($bankInfos);
        return $this;
    }

    public function withPayableRuleWithCard($payableLocaleRule)
    {
        $this->paymentRestrictions->setCardIssuerRegion($payableLocaleRule);
        return $this;
    }

    public function withRestrictionPaymentType($paymentTypeArray)
    {
        $paymentTypes = null;
        foreach ($paymentTypeArray as $paymentType)
        {
            if (!isset($paymentTypes))
            {
                $paymentTypes .= ';';
            }

            $paymentTypes .= $paymentType;
        }

        $this->paymentRestrictions->setPaymentType($paymentTypes);
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
