<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-06
 * Time: 오전 12:36
 */

namespace com\skplanet\syruppay\token\tav;


use com\skplanet\syruppay\token\SyrupPayTokenBuilder;

class TransactionAuthenticationValueTest extends \PHPUnit_Framework_TestCase
{
    public static $json = "{\n" .
    "\t\"cardToken\": \"dT2bT-P5dvK0-3zCi9VByf_SUsXxLEmITJGLsWm_oYE\",\n" .
    "\t\"mctTransAuthId\": \"0f2e781e-1d38-4766-a635-8d906d3fdff7\",\n" .
    "\t\"ocTransAuthId\": \"TA20151130000000000020008\",\n" .
    "\t\"paymentAuthenticationDetail\": {\n" .
    "\t\t\"payMethod\": \"10\",\n" .
    "\t\t\"payAmount\": 1000,\n" .
    "\t\t\"offerAmount\": 0,\n" .
    "\t\t\"loyaltyAmount\": 0,\n" .
    "\t\t\"payInstallment\": \"00\",\n" .
    "\t\t\"payCurrency\": \"KRW\",\n" .
    "\t\t\"payFinanceCode\": \"17\",\n" .
    "\t\t\"isCardPointApplied\": false\n" .
    "\t}\n" .
    "}";

    public static function getTransactionAuthenticationValue()
    {
        $j = json_decode(self::$json);
        $t = SyrupPayTokenBuilder::fromJson(new TransactionAuthenticationValue(), $j);
        return $t;
    }

    public function testGetChecksumBy()
    {
        $v = self::getTransactionAuthenticationValue();

        $cs = $v->getChecksumBy("KEY");

        $this->assertNotNull($cs);
        $this->assertGreaterThan(0, strlen($cs));
    }

    public function testIsValidBy()
    {
        $v = self::getTransactionAuthenticationValue();

        $cs = $v->getChecksumBy("KEY");
        $b = $v->isValidBy("KEY", $cs);

        $this->assertNotNull($cs);
        $this->assertTrue($b);
    }

    public function testIsValidBy_잘못된_체크섬()
    {
        $v = self::getTransactionAuthenticationValue();

        $b = $v->isValidBy("KEY", "WRONG CHECKSUM");

        $this->assertNotNull($b);
        $this->assertFalse($b);
    }

    public function testIsValidBy_잘못된_키()
    {
        $v = self::getTransactionAuthenticationValue();

        $cs = $v->getChecksumBy("KEY");
        $b = $v->isValidBy("WRONG_KEY", $cs);

        $this->assertNotNull($b);
        $this->assertFalse($b);
    }

    public function testIsValidBy_잘못된_키와_잘못된_체크섬()
    {
        $v = self::getTransactionAuthenticationValue();

        $b = $v->isValidBy("WRONG_KEY", "WRONG CHECKSUM");

        // Then
        $this->assertNotNull($b);
        $this->assertFalse($b);
    }
}
