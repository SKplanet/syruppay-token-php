<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-03
 * Time: 오후 12:09
 */

namespace com\skplanet\syruppay\token;


abstract class ClaimConfigurerAdapter extends PropertyMapper implements ClaimConfigurer
{
    private $builder;

    public function init($builder) {}
    public function configure($builder) {}

    public function next()
    {
        $builder = new SyrupPayTokenBuilder();
        $builder = $this->getBuilder();

        return $builder;
    }

    protected function getBuilder()
    {
        if (!isset($this->builder))
        {
            throw new InvalidStateException("builder cannot be null");
        }

        return $this->builder;
    }

    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }
}
