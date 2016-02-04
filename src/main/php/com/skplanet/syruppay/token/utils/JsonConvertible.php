<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-03
 * Time: 오전 11:35
 */

namespace com\skplanet\syruppay\token\utils;

abstract class JsonConvertible
{
    static function fromJson($json)
    {
        $result = new static();
        $objJson = json_decode($json);
        $class = new \ReflectionClass($result);
        $publicProps = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($publicProps as $prop)
        {
            $propName = $prop->name;
            if (isset($objJson->$propName))
            {
                $prop->setValue($result, $objJson->$propName);
            }
            else
            {
                $prop->setValue($result, null);
            }
        }
        return $result;
    }

    function toJson()
    {
        return json_encode($this);
    }

    public function jsonSerialize() {
        $propertyNames = get_object_vars($this);
        $jsonProperty = array();
        foreach ($propertyNames as $key => $value)
        {
            if (isset($value))
            {
                $jsonProperty[$key] = $value;
            }
        }

        return $jsonProperty;
    }
}
