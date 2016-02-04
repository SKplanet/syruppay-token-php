<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-03
 * Time: 오전 11:48
 */

namespace com\skplanet\syruppay\token;


interface ClaimConfigurer
{
    function claimName();
    function init($builder);
    function configure($builder);
    function validRequired();
}
