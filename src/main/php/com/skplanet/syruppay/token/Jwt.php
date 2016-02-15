<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-02
 * Time: 오후 6:46
 */

namespace com\skplanet\syruppay\token;


use com\skplanet\syruppay\token\utils\UUID;

class Jwt extends PropertyMapper
{
    protected $aud = "https://pay.syrup.co.kr";
    protected $typ = "jose";
    protected $iss;
    protected $exp;
    protected $iat;
    protected $jti;
    protected $nbf;
    protected $sub;

    function __construct()
    {
        $this->jti = UUID::v4();
    }

    function setSub($sub)
    {
        $this->sub = $sub;
    }

    function setIss($iss)
    {
        $this->iss = $iss;
    }

    function getIat()
    {
        return $this->iat;
    }

    function setIat($iat)
    {
        $this->iat = $iat;
    }

    function getExp()
    {
        return $this->exp;
    }

    function setExp($exp)
    {
        $this->exp = $exp;
    }

    function setNbf($nbf)
    {
        $this->nbf = $nbf;
    }
}
