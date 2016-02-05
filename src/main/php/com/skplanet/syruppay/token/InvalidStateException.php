<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-05
 * Time: 오후 5:08
 */

namespace com\skplanet\syruppay\token;


class InvalidStateException extends \Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
