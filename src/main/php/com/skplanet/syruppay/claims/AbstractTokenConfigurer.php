<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-03
 * Time: 오후 2:11
 */

namespace com\skplanet\syruppay\claims;


use com\skplanet\syruppay\token\ClaimConfigurerAdapter;

abstract class AbstractTokenConfigurer extends ClaimConfigurerAdapter
{
    public function disable()
    {
        $this->getBuilder()->removeConfigurer(get_class($this));
        return $this->getBuilder();
    }
}
