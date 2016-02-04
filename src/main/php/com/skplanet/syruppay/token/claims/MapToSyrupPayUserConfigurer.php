<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-03
 * Time: 오후 2:15
 */

namespace com\skplanet\syruppay\token\claims;


use com\skplanet\syruppay\token\utils\ClassPropertyUtils;

class MapToSyrupPayUserConfigurer extends AbstractTokenConfigurer
{
    use ClassPropertyUtils;

    private $mappingType;
    private $mappingValue;

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
