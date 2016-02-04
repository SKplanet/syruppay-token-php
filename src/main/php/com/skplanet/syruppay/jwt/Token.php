<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-02
 * Time: 오후 5:25
 */

namespace com\skplanet\syruppay\jwt;


interface Token extends JwtToken
{
    function getLoginInfo();
    function getTransactionInfo();
    function getUserInfoMapper();
    function isValidInTime();
    function getLineInfo();
    function getCheckoutInfo();
}
