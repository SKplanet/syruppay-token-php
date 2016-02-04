<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-03
 * Time: 오전 11:49
 */

namespace com\skplanet\syruppay\token;


abstract class AbstractConfiguredTokenBuilder extends AbstractClaimBuilder
{
    private $configurers = array();

    public function getConfigurer($className)
    {
        if (in_array($className, $this->configurers)) {
            $config = $this->configurers[$className];
            if (!isset($config)) {
                return null;
            }

            return $config;
        }

        return null;
    }

    public function removeConfigurer($className)
    {
        $config = $this->getConfigurer($className);
        unset($this->configurers[$className]);
        return $config;
    }

    public function apply($configurer)
    {
        $this->add($configurer);
        $configurer->setBuilder($this);
        return $configurer;
    }

    public function add($configurer)
    {
        $className = get_class($configurer);
        $this->configurers[$className] = $configurer;
    }

    public function getClasses()
    {
        return $this->configurers;
    }
}
