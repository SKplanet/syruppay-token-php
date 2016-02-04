<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-03
 * Time: 오전 11:42
 */

namespace com\skplanet\syruppay\token;

abstract class AbstractClaimBuilder implements ClaimBuilder
{
    private $isBuild = false;
    private $object;

    public function build()
    {
        if (!$this->isBuild)
        {
            $this->object = $this->doBuild();
            $this->isBuild = true;
            return $this->object;
        }

        throw new AlreadyBuiltException("This object has already been built");
    }

    public function getObject()
    {
        if (!isBuild)
        {
            throw new InvalidStateException("This object has not been built");
        }

        return $this->object;
    }

    protected abstract function doBuild();
}
