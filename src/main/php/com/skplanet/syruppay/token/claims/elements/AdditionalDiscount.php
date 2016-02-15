<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-06
 * Time: 오전 1:09
 */

namespace com\skplanet\syruppay\token\claims\elements;


use com\skplanet\syruppay\token\PropertyMapper;

class AdditionalDiscount extends PropertyMapper implements Element
{
    protected $percentOff;
    protected $maxApplicableAmt;

    public function getPercentOff()
    {
        return $this->percentOff;
    }

    public function setPercentOff($percentOff)
    {
        if ($percentOff <= 0) {
            throw new \InvalidArgumentException("percentOff field should be bigger than 0. yours percentOff is : " . $percentOff);
        }
        $this->percentOff = $percentOff;
        return $this;
    }

    public function getMaxApplicableAmt()
    {
        return $this->maxApplicableAmt;
    }

    public function setMaxApplicableAmt($maxApplicableAmt)
    {
        if ($maxApplicableAmt <= 0) {
            throw new \InvalidArgumentException("maxApplicableAmt field should be bigger than 0. yours maxApplicableAmt is : " . $maxApplicableAmt);
        }
        $this->maxApplicableAmt = $maxApplicableAmt;
        return $this;
    }

    public function validRequired()
    {
        if ($this->percentOff <= 0) {
            throw new \InvalidArgumentException("percentOff field should be bigger than 0. yours percentOff is : " . $this->percentOff);
        }
        if ($this->maxApplicableAmt <= 0) {
            throw new \InvalidArgumentException("maxApplicableAmt field should be bigger than 0. yours maxApplicableAmt is : " . $this->maxApplicableAmt);
        }
    }
}
