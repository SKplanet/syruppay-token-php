<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-02
 * Time: 오후 4:51
 */

namespace com\skplanet\syruppay\token;


use com\skplanet\jose\Jose;
use com\skplanet\jose\JoseBuilders;
use com\skplanet\jose\JoseHeader;
use com\skplanet\jose\JoseHeaderSpec;
use com\skplanet\jose\jwa\Jwa;
use com\skplanet\syruppay\claims\MapToSyrupPayUserConfigurer;
use com\skplanet\syruppay\claims\MerchantUserConfigurer;
use com\skplanet\syruppay\claims\OrderConfigurer;
use com\skplanet\syruppay\claims\PayConfigurer;
use com\skplanet\syruppay\jwt\SyrupPayTokenPropertyMapper;

class SyrupPayTokenBuilder extends AbstractConfiguredTokenBuilder implements ClaimBuilder, TokenBuilder
{
    private $iss;
    private $nbf;
    private $sub;
    private $expiredMinutes = 10;
    private static $checkValidationOfToken = true;

    public function uncheckValidationOfToken()
    {
        SyrupPayTokenBuilder::$checkValidationOfToken = false;
    }

    public static function verify($token, $key)
    {
        $jose = new Jose();
        $payload = $jose->configuration(
            JoseBuilders::compactDeserializationBuilder()
            ->serializedSource($token)
            ->key($key)
        )->deserialization();

        $json = json_decode($payload, true);
        return $json;
    }

    public function of($merchantId)
    {
        $this->iss = $merchantId;
        return $this;
    }

    public function additionalSubject($subject)
    {
        $this->sub = $subject;
        return $this;
    }

    public function isNotValidBefore($milliseconds)
    {
        $this->nbf = $milliseconds / 1000;
        return $this;
    }

    public function expiredMinutes($expiredMinutes)
    {
        $this->expiredMinutes = $expiredMinutes;
    }

    public function login()
    {
        return $this->getOrApply(new MerchantUserConfigurer());
    }

    public function signUp()
    {
        return $this->getOrApply(new MerchantUserConfigurer());
    }

    public function pay()
    {
        return $this->getOrApply(new PayConfigurer());
    }

    public function checkout()
    {
        return $this->getOrApply(new OrderConfigurer());
    }

    public function mapToSyrupPayUser()
    {
        return $this->getOrApply(new MapToSyrupPayUserConfigurer());
    }

    private function getOrApply($configurer)
    {
        $existingConfig = $this->getConfigurer(get_class($configurer));
        if (isset($existingConfig)) {
            return $existingConfig;
        }

        return $this->apply($configurer);
    }

    protected function doBuild()
    {
        if (!isset($this->iss)) {
            throw new \InvalidArgumentException("issuer couldn't be null. you should set of by SyrupPayTokenBuilder#of(String of)");
        }

        $jwt = new Jwt();
        $jwt->setIss($this->iss);
        $jwt->setIat(time());
        $jwt->setExp($jwt->getIat() + ($this->expiredMinutes * 60));
        $jwt->setNbf($this->nbf);
        $jwt->setSub($this->sub);

        return $jwt;
    }

    public function generateTokenBy($secret)
    {
        $jose = new Jose();
        return $jose->configuration(
            JoseBuilders::JsonSignatureCompactSerializationBuilder()
            ->header(new JoseHeader(
                array(JoseHeaderSpec::ALG => Jwa::HS256,
                    JoseHeaderSpec::TYP => 'JWT',
                    JoseHeaderSpec::KID => $this->iss)))
            ->payload($this->toJson())
            ->key($secret)
        )->serialization();
    }

    public function toJson()
    {
        $propertyArray = array();

        $jwt = $this->build();
        if (isset($jwt) && $jwt instanceof Jwt) {
            $propertyArray = array_merge($propertyArray, $jwt->__toArray());
        }

        $configurers = $this->getClasses();
        foreach ($configurers as $className => $configurer) {
            if (isset($configurer)) {
                $configurer->validRequired();
                $value = $configurer->__toArray();
                $claimName = $configurer->claimName();

                $propertyArray[$claimName] = $value;
            }
        }

        return json_encode($propertyArray);
    }
}
