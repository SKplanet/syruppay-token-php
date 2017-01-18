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

abstract class syruppay_token_ClaimConfigurerAdapter extends syruppay_token_PropertyMapper implements syruppay_token_ClaimConfigurer
{
    private $builder;

    public function init($builder) {}
    public function configure($builder) {}

    public function next()
    {
        $builder = new syruppay_token_SyrupPayTokenBuilder();
        $builder = $this->getBuilder();

        return $builder;
    }

    protected function getBuilder()
    {
        if (!isset($this->builder))
        {
            throw new syruppay_token_InvalidStateException("builder cannot be null");
        }

        return $this->builder;
    }

    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }
}
