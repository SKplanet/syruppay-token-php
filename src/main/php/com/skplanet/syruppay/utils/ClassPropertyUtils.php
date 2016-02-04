<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-04
 * Time: 오후 2:18
 */

namespace com\skplanet\syruppay\utils;


trait ClassPropertyUtils
{
    public function __toArray()
    {
        $properties = get_object_vars($this);
        $propertyMap = array();

        foreach ($properties as $pKey => $pValue) {
            if (isset($pValue)) {
                $value = $pValue;
                if (is_object($pValue)) {
                    if (method_exists($pValue, "__toArray")) {
                        $value = call_user_func(array($pValue, '__toArray'));
                    } else {
                        continue;
                    }
                } else if (is_array($pValue)) {
                    $arValue = array();
                    foreach ($pValue as $pArKey => $pArValue) {
                        if (method_exists($pArValue, "__toArray")) {
                            $arValue[$pArKey] = call_user_func(array($pArValue, '__toArray'));
                        } else {
                            $arValue[] = $pArValue;
                        }
                    }

                    $value = $arValue;
                }

                $propertyMap[$pKey] = $value;
            }
        }

        return $propertyMap;
    }

    public function __fromJson(array $jsonDecodedArray, $targetObject)
    {
//        $reflectionClass = new ReflectionClass(get_class($targetObject));
        foreach ($jsonDecodedArray as $propertyName => $value) {
            $targetObject->{'set'.ucwords($propertyName)}($value);
        }
    }
}
