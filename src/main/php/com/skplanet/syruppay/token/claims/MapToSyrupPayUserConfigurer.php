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

    public function withValue($value)
    {
        $this->mappingValue = $value;
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
            throw new \InvalidArgumentException("fields to map couldn't be null. type : ".$this->mappingType." value : ".$this->mappingValue);
        }
    }
}

class MappingType
{
    const CI_HASH = "CI_HASH";
    const CI_MAPPED_KEY = "CI_MAPPED_KEY";
}
