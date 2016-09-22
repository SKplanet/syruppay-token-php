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

namespace syruppay\token;


use syruppay\jose\Jose;
use syruppay\jose\JoseBuilders;
use syruppay\jose\JoseHeader;
use syruppay\jose\JoseHeaderSpec;
use syruppay\jose\jwa\Jwa;
use syruppay\token\claims\MapToSyrupPayUserConfigurer;
use syruppay\token\claims\MerchantUserConfigurer;
use syruppay\token\claims\OrderConfigurer;
use syruppay\token\claims\PayConfigurer;
use syruppay\token\claims\SubscriptionConfigurer;
use syruppay\token\jwt\SyrupPayToken;
use syruppay\token\utils\JsonPrettyPrint;

class SyrupPayTokenBuilder extends AbstractConfiguredTokenBuilder implements ClaimBuilder, TokenBuilder
{
    private $iss;
    private $nbf;
    private $sub;
    private $expiredMinutes = 10;
    private static $checkValidationOfToken = true;

    public static function uncheckValidationOfToken()
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

        $syrupPayToken = self::fromJson(new SyrupPayToken(), json_decode($payload));
        if (self::$checkValidationOfToken && !$syrupPayToken->isValidInTime()) {
            throw new \InvalidArgumentException(sprintf("%d as exp of this token is over at now as %d", $syrupPayToken->getExp(), time()));
        }

        return $syrupPayToken;
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
        $merchantUserConfigurer = new MerchantUserConfigurer();
        $this->getOrApply($merchantUserConfigurer);

        return $merchantUserConfigurer;
    }

    public function signUp()
    {
        $merchantUserConfigurer = new MerchantUserConfigurer();
        $this->getOrApply($merchantUserConfigurer);

        return $merchantUserConfigurer;
    }

    public function pay()
    {
        $payConfigurer = new PayConfigurer();
        $this->getOrApply($payConfigurer);

        return $payConfigurer;
    }

    public function checkout()
    {
        $orderConfigurer = new OrderConfigurer();
        $this->getOrApply($orderConfigurer);

        return $orderConfigurer;
    }

    public function mapToSyrupPayUser()
    {
        $mapToSyrupPayUserConfigurer = new MapToSyrupPayUserConfigurer();
        $this->getOrApply($mapToSyrupPayUserConfigurer);

        return $mapToSyrupPayUserConfigurer;
    }

    public function subscription()
    {
        $subscription = new SubscriptionConfigurer();
        $this->getOrApply($subscription);

        return $subscription;
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

        $json = json_encode($propertyArray);
//        echo JsonPrettyPrint::prettyPrint($json);
        return $json;
    }

    public static function fromJson($dest, \stdClass $src)
    {
        $srcReflection = new \ReflectionObject($src);
        $srcProperties = $srcReflection->getProperties();

        $destReflection = new \ReflectionObject($dest);
        foreach ($srcProperties as $srcProperty) {
            $propertyName = $srcProperty->getName();
            $propertyValue = $srcProperty->getValue($src);

            if ($destReflection->hasProperty($propertyName)) {
                if (is_object($propertyValue)) {    //single custom class variable
                    $className = self::getAnnotation($dest, $propertyName);
                    if (!isset($className)) {
                        continue;
                    }

                    if (empty($className)) {
                        throw new \InvalidArgumentException("No declared class name. There is annotaion '@var' missing at document comment: $propertyName");
                    }

                    $newClassObject = self::fromJson(new $className(), $propertyValue);
                    self::injectValue($dest, $propertyName, $newClassObject);
                } else if (is_array($propertyValue)) {  //custom class list or primitive list
                    $className = SyrupPayTokenBuilder::getAnnotation($dest, $propertyName);

                    //case on primitive list
                    if (!isset($className) || empty($className)) {
                        self::injectValue($dest, $propertyName, $propertyValue);
                        continue;
                    }

                    //case on custom list
                    $arrayNewClassObject = array();
                    foreach ($propertyValue as $arrayKey => $arrayValue) {  //arrayKey : 0, arrayValue : stdClass
                        $newClassObject = self::fromJson(new $className, $arrayValue);
                        $arrayNewClassObject[] = $newClassObject;
                    }

                    self::injectValue($dest, $propertyName, $arrayNewClassObject);
                } else {
                    self::injectValue($dest, $propertyName, $propertyValue);
                }
            }
        }

        return $dest;
    }

    private function getAnnotation($dest, $propertyName)
    {
        $destReflection = new \ReflectionObject($dest);
        $destProperty = $destReflection->getProperty($propertyName);
        $comment = $destProperty->getDocComment();
        if (isset($comment)) {
            $comment = substr($comment, 3, -2);
            $className = preg_replace('/^\s*\*\s*@\s*var\s*/i', '', $comment);
            $className = preg_replace('/\s+/', '', $className);

            return $className;
        }

        return null;
    }

    private function injectValue($dest, $propertyName, $value)
    {
        $destReflection = new \ReflectionObject($dest);
        $destProperty = $destReflection->getProperty($propertyName);
        $destProperty->setAccessible(true);
        $destProperty->setValue($dest, $value);
    }
}