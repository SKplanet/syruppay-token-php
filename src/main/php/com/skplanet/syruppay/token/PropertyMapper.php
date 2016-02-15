<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-15
 * Time: 오후 1:17
 */

namespace com\skplanet\syruppay\token;


class PropertyMapper
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
}
