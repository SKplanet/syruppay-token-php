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

class syruppay_token_PropertyMapper
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
                } else if (is_array($pValue) && !empty($pValue)) {
                    $arValue = array();
                    foreach ($pValue as $pArKey => $pArValue) {
                        if (method_exists($pArValue, "__toArray")) {
                            $arValue[$pArKey] = call_user_func(array($pArValue, '__toArray'));
                        } else {
                            $arValue[] = $pArValue;
                        }
                    }

                    $value = $arValue;
                } else if (is_array($pValue) && empty($pValue)) {
                    continue;
                }

                $propertyMap[$pKey] = $value;
            }
        }

        return $propertyMap;
    }
}
