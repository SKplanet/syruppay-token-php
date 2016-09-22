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


use syruppay\jose\Jose;
use syruppay\jose\JoseBuilders;
use syruppay\jose\JoseHeader;
use syruppay\jose\JoseHeaderSpec;
use syruppay\jose\jwa\Jwa;

class MapToSyrupPayUserConfigurer extends AbstractTokenConfigurer
{
    protected $mappingType;
    protected $mappingValue;
    protected $identityAuthenticationId;

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

    function withIdentityAuthenticationId($identityAuthenticationId)
    {
        $this->identityAuthenticationId = $identityAuthenticationId;
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
