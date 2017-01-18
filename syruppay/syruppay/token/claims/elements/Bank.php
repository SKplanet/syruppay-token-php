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

class syruppay_token_claims_elements_Bank extends syruppay_token_PropertyMapper
{
    protected $bankCode;

    public function __construct()
    {
        if (func_num_args() == 1)
        {
            $bankCodes = func_get_arg(0);
            if (is_array($bankCodes))
            {
                foreach ($bankCodes as $bankCode)
                {
                    if (isset($this->bankCode))
                        $this->bankCode .= ":";
                    $this->bankCode .= $bankCode;
                }
            }
            else
            {
                throw new InvalidArgumentException("bankCode is array type. ex) array('bankCode', 'bankCode')");
            }
        }
    }

    public function getBankCode()
    {
        return $this->bankCode;
    }
}
