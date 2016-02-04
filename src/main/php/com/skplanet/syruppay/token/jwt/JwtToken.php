<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-02
 * Time: 오후 5:25
 */

namespace com\skplanet\syruppay\token\jwt;


interface JwtToken
{
    function getIss();
    function getSub();
    function getAud();
    function getExp();
    function getNbf();
    function getIat();
    function getJti();
}
