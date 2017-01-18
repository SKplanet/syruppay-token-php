<?php
/*
 * The MIT License (MIT)
 * Copyright (c) 2015 SK PLANET. All Rights Reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the 'Software'), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

define('ACCEPTTYPE_CARD', 'CARD');
define('ACCEPTTYPE_BANK', 'BANK');
define('ACCEPTTYPE_MOBILE', 'MOBILE');
define('ACCEPTTYPE_SYRUP_PAY_COUPON', 'SYRUP_PAY_COUPON');

define('CARDTYPE_CREDIT', 'CC');
define('CARDTYPE_CHECK', 'CH');

function getCardTypes()
{
    return array(CARDTYPE_CREDIT, CARDTYPE_CHECK);
}

define('CASHRECEIPTDISPLAY_YES', 'YES');
define('CASHRECEIPTDISPLAY_NO', 'NO');
define('CASHRECEIPTDISPLAY_DELEGATE_ADMIN', 'DELEGATE_ADMIN');

function getCashReceiptDisplays()
{
    return array(CASHRECEIPTDISPLAY_YES, CASHRECEIPTDISPLAY_NO, CASHRECEIPTDISPLAY_DELEGATE_ADMIN);
}

define('CURRENCY_KRW', 'KRW');
define('CURRENCY_USD', 'USD');

define('DELIVERYRESTRICTION_NOT_FAR_AWAY', 'NOT_FAR_AWAY');
define('DELIVERYRESTRICTION_FAR_AWAY', 'FAR_AWAY');
define('DELIVERYRESTRICTION_FAR_FAR_AWAY', 'FAR_FAR_AWAY');


define('DELIVERYTYPE_PREPAID', 'PREPAID');
define('DELIVERYTYPE_FREE', 'FREE');
define('DELIVERYTYPE_DIY', 'DIY');
define('DELIVERYTYPE_QUICK', 'QUICK');
define('DELIVERYTYPE_PAYMENT_ON_DELIVERY', 'PAYMENT_ON_DELIVERY');

define('ERRORTYPE_MAINTENACE', 'MAINTENACE');
define('ERRORTYPE_SYSTEM_ERR', 'SYSTEM_ERR');

define('INTERVAL_ONDEMAND', 'ONDEMAND');
define('INTERVAL_MONTHLY', 'MONTHLY');
define('INTERVAL_WEEKLY', 'WEEKLY');
define('INTERVAL_BIWEEKLY', 'BIWEEKLY');

function getInterval()
{
    return array(INTERVAL_ONDEMAND, INTERVAL_MONTHLY, INTERVAL_WEEKLY, INTERVAL_BIWEEKLY);
}

define('LANGUAGE_KO', 'KO');
define('LANGUAGE_EN', 'EN');

define('LOYALTYID_POINT_OF_11ST', 'www.11st.co.kr:point');
define('LOYALTYID_MILEAGE_OF_11ST', 'www.11st.co.kr:mileage');
define('LOYALTYID_T_MEMBERSHIP', 'www.sktmemebership.co.kr');
define('LOYALTYID_OK_CASHBAG', 'www.okcashbag.com');


define('MAPPINGTYPE_CI_HASH', 'CI_HASH');
define('MAPPINGTYPE_CI_MAPPED_KEY', 'CI_MAPPED_KEY');
define('MAPPINGTYPE_ENCRYPTED_PERSONAL_INFO', 'ENCRYPTED_PERSONAL_INFO');


define('MATCHEDUSER_CI_MATCHED_ONLY', 'CI_MATCHED_ONLY');
define('MATCHEDUSER_FIRST_SIGNUP_IN_LIFETIME_ONLY', 'FIRST_SIGNUP_IN_LIFETIME_ONLY');

define('OFFERTYPE_DELIVERY_COUPON', 'DELIVERY_COUPON');

define('OPERATORCODE_SKT', 'SKT');
define('OPERATORCODE_KT', 'KT');
define('OPERATORCODE_LGU', 'LGU');
define('OPERATORCODE_SKTM', 'SKTM');
define('OPERATORCODE_KTM', 'KTM');
define('OPERATORCODE_LGUM', 'LGUM');
define('OPERATORCODE_UNKNOWN', 'UNKNOWN');

define('PAYABLELOCALERULE_ONLY_ALLOWED_KOR', 'ALLOWED:KOR');
define('PAYABLELOCALERULE_ONLY_NOT_ALLOWED_KOR', 'NOT_ALLOWED:KOR');
define('PAYABLELOCALERULE_ONLY_ALLOWED_USA', 'ALLOWED:USA');
define('PAYABLELOCALERULE_ONLY_NOT_ALLOWED_USA', 'NOT_ALLOWED:USA');

function getPayableLocaleRules()
{
    return array(PAYABLELOCALERULE_ONLY_ALLOWED_KOR, PAYABLELOCALERULE_ONLY_NOT_ALLOWED_KOR, PAYABLELOCALERULE_ONLY_ALLOWED_USA, PAYABLELOCALERULE_ONLY_NOT_ALLOWED_USA);
}

define('PAYMENTTYPE_CARD', 'CARD');
define('PAYMENTTYPE_BANK', 'BANK');
define('PAYMENTTYPE_MOBILE', 'MOBILE');