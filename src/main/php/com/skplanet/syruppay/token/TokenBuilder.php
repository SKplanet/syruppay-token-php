<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-02
 * Time: 오후 4:56
 */

namespace com\skplanet\syruppay\token;


interface TokenBuilder extends ClaimBuilder
{
    function getConfigurer($className);
    function removeConfigurer($className);
}
