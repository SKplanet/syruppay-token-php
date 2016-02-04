<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-04
 * Time: 오후 1:07
 */

namespace com\skplanet\syruppay\token\utils;


trait JsonConverter
{
    public function __toArray()
    {
        $propertyNames = get_object_vars($this);
        $jsonProperty = array();

        foreach ($propertyNames as $key => $value) {
            if (isset($value)) {
                $jsonProperty[$key] = $value;
            }
        }

        return $jsonProperty;
    }

    public function __toArray1()
    {
        $className = get_class($this);
        $reflector = new \ReflectionClass($className);
        $properties = $reflector->getProperties();

//        var_dump($properties);


        $propertyNames = get_object_vars($this);
//        var_dump($propertyNames);
        $jsonProperty = array();

        foreach ($propertyNames as $pKey => $pValue) {


            if (isset($pValue)) {
                $value = $pValue;

                if (is_object($pValue)) {
                    if (method_exists($pValue, "__toArray1")) {
                        $value = call_user_func(array($pValue, '__toArray1'));
                    } else {
                        continue;
                    }
                }

                $jsonProperty[$pKey] = $value;
            }
        }

        return $jsonProperty;
    }
}
