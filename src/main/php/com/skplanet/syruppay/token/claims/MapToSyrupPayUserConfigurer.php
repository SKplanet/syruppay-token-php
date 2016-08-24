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


use com\skplanet\jose\Jose;
use com\skplanet\jose\JoseBuilders;
use com\skplanet\jose\JoseHeader;
use com\skplanet\jose\JoseHeaderSpec;
use com\skplanet\jose\jwa\Jwa;
use com\skplanet\syruppay\token\PropertyMapper;
use com\skplanet\syruppay\token\utils\JsonPrettyPrint;

class MapToSyrupPayUserConfigurer extends AbstractTokenConfigurer
{
    protected $mappingType;
    protected $mappingValue;

    public function getMappingType()
    {
        return $this->mappingType;
    }

    public function getMappingValue()
    {
        return $this->mappingValue;
    }

    public function withType($type)
    {
        $this->mappingType = $type;
        return $this;
    }

    public function withValue()
    {
        $argc = func_num_args();
        if ($argc == 1)
        {
            $this->mappingValue = func_get_arg(0);
        }
        else if ($argc == 3)
        {
            $personal = func_get_arg(0);
            $kid = func_get_arg(1);
            $secret = func_get_arg(2);

            if (!isset($personal) || empty($kid) || empty($secret))
            {
                throw new \InvalidArgumentException("Personal Object or kid or secret may be null.");
            }

            $json = json_encode($personal->__toArray());
//            var_dump(JsonPrettyPrint::prettyPrint($json));

            $jose = new Jose();
            $this->mappingValue = $jose->configuration(
                JoseBuilders::JsonEncryptionCompactSerializationBuilder()
                ->header(new JoseHeader(
                    array(JoseHeaderSpec::ALG => Jwa::A256KW,
                        JoseHeaderSpec::ENC => Jwa::A128CBC_HS256,
                        JoseHeaderSpec::KID => $kid)))
                ->payload($json)
                ->key($secret)
            )->serialization();
        }
        else
        {
            throw new \InvalidArgumentException("Unknown parameter.");
        }

        return $this;
    }

    function claimName()
    {
        return "userInfoMapper";
    }

    function validRequired()
    {
        if (!isset($this->mappingType) || !isset($this->mappingValue))
        {
            throw new \InvalidArgumentException("fields to map couldn't be null. type : $this->mappingType value : $this->mappingValue");
        }
    }
}

class MappingType
{
    const CI_HASH = "CI_HASH";
    const CI_MAPPED_KEY = "CI_MAPPED_KEY";
    const ENCRYPTED_PERSONAL_INFO = "ENCRYPTED_PERSONAL_INFO";
}

class Personal extends PropertyMapper
{
    protected $username;
    protected $lineNumber;
    protected $operatorCode;
    protected $ssnFirst7Digit;
    protected $email;
    protected $ciHash;
    /**
     * @var com\skplanet\syruppay\token\claims\PayableCard
     */
    protected $payableCard;

    public function setUsername($username)
    {
        if (empty($username))
        {
            throw new \InvalidArgumentException("username shouldn't be null and not empty.");
        }

        $this->username = $username;
        return $this;
    }

    public function setLineNumber($lineNumber)
    {
        if (empty($lineNumber))
        {
            throw new \InvalidArgumentException("lineNumber shouldn't be null and not empty.");
        }
        $this->lineNumber = $lineNumber;
        return $this;
    }

    public function setOperatorCode($operatorCode)
    {
        $this->operatorCode = $operatorCode;
        return $this;
    }

    public function setSsnFirst7Digit($ssnFirst7Digit)
    {
        if (empty($ssnFirst7Digit)) {
            throw new \InvalidArgumentException("ssnFirst7Digit shouldn't be null and not empty.");
        }

        if (strlen($ssnFirst7Digit) != 7)
        {
            throw new \InvalidArgumentException("length of ssnFirst7Digit should be 7. yours inputs is : $ssnFirst7Digit");
        }

        $this->ssnFirst7Digit = $ssnFirst7Digit;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setCiHash($ciHash)
    {
        $this->ciHash = $ciHash;
        return $this;
    }

    public function setPayableCard($payableCard)
    {
        $this->payableCard = $payableCard;
        return $this;
    }

    public static function of()
    {
        return new Personal();
    }
}

class OperatorCode
{
    const SKT   = "SKT";
    const KT    = "KT";
    const LGU   = "LGU";
    const SKTM  = "SKTM";
    const KTM   = "KTM";
    const LGUM  = "LGUM";
    const UNKNOWN = "UNKNOWN";
}

class PayableCard extends PropertyMapper
{
    protected $cardNo;
    protected $expireDate;
    protected $cardIssuer;
    protected $cardIssuerName;
    protected $cardName;
    protected $cardNameInEnglish;
    protected $cardAcquirer;
    protected $cardType;

    public function setCardNo($cardNo)
    {
        if (empty($cardNo))
        {
            throw new \InvalidArgumentException("cardNo shouldn't be null and not empty.");
        }

        $this->cardNo = $cardNo;
        return $this;
    }

    public function setExpireDate($expireDate)
    {
        if (empty($expireDate))
        {
            throw new \InvalidArgumentException("expireDate shouldn't be null and not empty.");
        }

        $this->expireDate = $expireDate;
        return $this;
    }

    public function setCardIssuer($cardIssuer)
    {
        if (empty($cardIssuer))
        {
            throw new \InvalidArgumentException("cardIssuer shouldn't be null and not empty.");
        }

        $this->cardIssuer = $cardIssuer;
        return $this;
    }

    public function setCardIssuerName($cardIssuerName)
    {
        if (empty($cardIssuerName))
        {
            throw new \InvalidArgumentException("cardIssuerName shouldn't be null and not empty.");
        }

        $this->cardIssuerName = $cardIssuerName;
        return $this;
    }

    public function setCardName($cardName)
    {
        if (empty($cardName))
        {
            throw new \InvalidArgumentException("cardNo shouldn't be null and not empty.");
        }

        $this->cardName = $cardName;
        return $this;
    }

    public function setCardNameInEnglish($cardNameInEnglish)
    {
        $this->cardNameInEnglish = $cardNameInEnglish;
        return $this;
    }

    public function setCardAcquirer($cardAcquirer)
    {
        if (empty($cardAcquirer))
        {
            throw new \InvalidArgumentException("cardAcquirer shouldn't be null and not empty.");
        }

        $this->cardAcquirer = $cardAcquirer;
        return $this;
    }

    public function setCardType($cardType)
    {
        if (!in_array($cardType, CardType::getCardTypes()))
        {
            throw new \InvalidArgumentException("cardType shouldn't be null and it should be one of CardType constants.");
        }

        $this->cardType = $cardType;
        return $this;
    }

    public static function of()
    {
        return new PayableCard();
    }

}

class CardType
{
    const CREDIT = "CC";
    const CHECK  = "CH";

    public static function getCardTypes()
    {
        return array(CardType::CREDIT, CardType::CHECK);
    }

}
