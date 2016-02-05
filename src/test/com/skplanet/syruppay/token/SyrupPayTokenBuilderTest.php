<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-05
 * Time: 오후 3:43
 */

namespace com\skplanet\syruppay\token;


use com\skplanet\syruppay\token\claims\CardInstallmentInformation;
use com\skplanet\syruppay\token\claims\Currency;
use com\skplanet\syruppay\token\claims\Language;
use com\skplanet\syruppay\token\claims\MappingType;
use com\skplanet\syruppay\token\claims\PayableLocaleRule;
use com\skplanet\syruppay\token\claims\ShippingAddress;
use com\skplanet\syruppay\token\jwt\SyrupPayToken;

class SyrupPayTokenBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_MctAccToken_형식으로_회원정보_없이_생성()
    {
        $builder = new SyrupPayTokenBuilder();
        $builder->of("가맹점");
        $s = $builder->generateTokenBy("keys");

        $this->assertNotNull($s);
        $this->assertNotEmpty($s);
    }

    public function test_MctAccToken_형식으로_회원정보_포함하여_생성()
    {
        $builder = new SyrupPayTokenBuilder();
        $s = $builder->of("가맹점")
        ->login()
        ->withMerchantUserId("가맹점의 회원 ID 또는 식별자")
        ->withExtraMerchantUserId("핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력")
        ->withSsoCredential("SSO 를 발급 받았을 경우 입력");

        $s = $builder->generateTokenBy("keys");

        $this->assertNotNull($s);
        $this->assertNotEmpty($s);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_iss_미입력_후_빌드()
    {
        $builder = new SyrupPayTokenBuilder();
        $builder->build();
    }

    public function test_유효시간_기본값으로_입력_후_10분_여부_검증()
    {
        $builder = new SyrupPayTokenBuilder();
        $builder->of("test");
        $json = $builder->toJson();

        $token = SyrupPayTokenBuilder::fromJson(new SyrupPayToken(), json_decode($json));
        $this->assertNotNull($token);
        $this->assertEquals(10 * 60, $token->getExp() - $token->getIat());
    }

    public function test_유효시간_60분으로_입력_후_검증()
    {
        $builder = new SyrupPayTokenBuilder();
        $builder->of("test");
        $builder->expiredMinutes(60);

        $json = $builder->toJson();

        $token = SyrupPayTokenBuilder::fromJson(new SyrupPayToken(), json_decode($json));
        $this->assertNotNull($token);
        $this->assertEquals(60 * 60, $token->getExp() - $token->getIat());
    }

    public function test_유효시간_0분으로_입력_후_검증()
    {
        $builder = new SyrupPayTokenBuilder();
        $builder->of("test");
        $builder->expiredMinutes(0);

        $json = $builder->toJson();

        $token = SyrupPayTokenBuilder::fromJson(new SyrupPayToken(), json_decode($json));
        $this->assertNotNull($token);
        $this->assertEquals($token->getExp(), $token->getIat());
    }

    public function test_유효시간_마이너스_1분으로_입력_후_만료여부_검증()
    {
        $builder = new SyrupPayTokenBuilder();
        $builder->of("test");
        $builder->expiredMinutes(-1);

        $json = $builder->toJson();

        $token = SyrupPayTokenBuilder::fromJson(new SyrupPayToken(), json_decode($json));
        $this->assertNotNull($token);
        $this->assertEquals(false, $token->isValidInTime());
    }

    public function test_시럽페이_사용자_매칭_정보_입력()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->login()
                    ->withMerchantUserId("가맹점의 회원 ID 또는 식별자")
                    ->withExtraMerchantUserId("핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력")
                    ->withSsoCredential("SSO 를 발급 받았을 경우 입력")
                ->next()
                ->mapToSyrupPayUser()
                    ->withType(MappingType::CI_MAPPED_KEY)
                    ->withValue('4987234');
        // @formatter:on

        $t = $builder->generateTokenBy("가맹점에게 전달한 비밀키");

        $this->assertNotNull($t);
        $this->assertFalse(empty($t));
    }

    public function test_이미_생성한_토큰빌드를_재활용()
    {
        $this->setExpectedException(AlreadyBuiltException::class);

        $builder = new SyrupPayTokenBuilder();
        $builder->of("가맹점")->generateTokenBy("가맹점에게 전달한 비밀키");
        $builder->generateTokenBy("가맹점에게 전달한 비밀키");
    }

    public function test_구매를_위한_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();
        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID")
                    ->withProductTitle("제품명")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(50000)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $t = $builder->generateTokenBy("가맹점에게 전달한 비밀키");

        $this->assertNotNull($t);
        $this->assertNotEmpty($t);
    }

    public function test_토큰_복호화()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $t = $builder->of("가맹점")
                    ->login()
                        ->withMerchantUserId("가맹점의 회원 ID 또는 식별자")
                        ->withExtraMerchantUserId("핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력")
                        ->withSsoCredential("SSO 를 발급 받았을 경우 입력")
                    ->next()
                    ->generateTokenBy("가맹점에게 전달한 비밀키");
        // @formatter:on

        $token = SyrupPayTokenBuilder::verify($t, "가맹점에게 전달한 비밀키");

        $this->assertNotNull($token);
        $this->assertTrue($token->isValidInTime());

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_가맹점_회원ID_미입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();
        $builder->of("가맹점")->login()->withSsoCredential("SSO 를 발급 받았을 경우 입력");
        $token = $builder->generateTokenBy("가맹점에게 전달한 비밀키");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_구매_시_구매_상품명_미입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID")
                    //->withProductTitle("제품명")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(50000)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $builder->generateTokenBy("가맹점에게 전달한 비밀키");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_구매_시_구매금액_미입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID")
                    ->withProductTitle("제품명")
                    ->withLanguageForDisplay(Language::KO)
//                    ->withAmount(50000)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $builder->generateTokenBy("가맹점에게 전달한 비밀키");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_구매_시_구매금액_마이너스_입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID")
                    ->withProductTitle("제품명")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(-1)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $builder->generateTokenBy("가맹점에게 전달한 비밀키");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_구매_시_구매금액_0_입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID")
                    ->withProductTitle("제품명")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(0)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $builder->generateTokenBy("가맹점에게 전달한 비밀키");
    }

    public function test_구매_시_구매금액_통화단위_미입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID")
                    ->withProductTitle("제품명")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(500000)
//                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $t = $builder->generateTokenBy("가맹점에게 전달한 비밀키");
        $token = SyrupPayTokenBuilder::verify($t, "가맹점에게 전달한 비밀키");

        $this->assertEquals("KRW", $token->getTransactionInfo()->getPaymentInfo()->getCurrencyCode());

    }

    public function test_구매_시_언어_미입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID")
                    ->withProductTitle("제품명")
//                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(500000)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $t = $builder->generateTokenBy("가맹점에게 전달한 비밀키");
        $token = SyrupPayTokenBuilder::verify($t, "가맹점에게 전달한 비밀키");

        $this->assertEquals("KO", $token->getTransactionInfo()->getPaymentInfo()->getLang());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_구매_시_구매금액_배송지_입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID")
                    ->withProductTitle("제품명")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(0)
                    ->withCurrency(Currency::KRW)
//                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $t = $builder->generateTokenBy("가맹점에게 전달한 비밀키");
    }

    public function test_구매_시_가맹점_주문ID_40자_입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("1234567890123456789012345678901234567890")
                    ->withProductTitle("제품명")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(500000)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $t = $builder->generateTokenBy("가맹점에게 전달한 비밀키");
        $token = SyrupPayTokenBuilder::verify($t, "가맹점에게 전달한 비밀키");

        $this->assertEquals(40, strlen($token->getTransactionInfo()->getMerchantTransactionAuthenticatedId()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_구매_시_가맹점_주문ID_41자_입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("12345678901234567890123456789012345678901")
                    ->withProductTitle("제품명")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(500000)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $t = $builder->generateTokenBy("가맹점에게 전달한 비밀키");
        $token = SyrupPayTokenBuilder::verify($t, "가맹점에게 전달한 비밀키");

        $this->assertEquals(40, strlen($token->getTransactionInfo()->getMerchantTransactionAuthenticatedId()));
    }

    public function test_구매_시_제품_상세_URL_추가_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("1234567890123456789012345678901234567890")
                    ->withProductTitle("제품명")
                    ->withProductUrls("http://www.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1354119088&trTypeCd=22&trCtgrNo=895019")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(500000)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $t = $builder->generateTokenBy("가맹점에게 전달한 비밀키");
        $token = SyrupPayTokenBuilder::verify($t, "가맹점에게 전달한 비밀키");

        $this->assertEquals(1, count($token->getTransactionInfo()->getPaymentInfo()->getProductUrls()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_구매_시_제품_상세_URL에_HTTP가_아닌_값_입력_후_토큰_생성()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $builder->of("가맹점")
                ->pay()
                    ->withOrderIdOfMerchant("1234567890123456789012345678901234567890")
                    ->withProductTitle("제품명")
                    ->withProductUrls("www.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1354119088&trTypeCd=22&trCtgrNo=895019")
                    ->withLanguageForDisplay(Language::KO)
                    ->withAmount(500000)
                    ->withCurrency(Currency::KRW)
                    ->withShippingAddress(new ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "KR"))
                    ->withDeliveryPhoneNumber("01011112222")
                    ->withDeliveryName("배송 수신자")
                    ->withInstallmentPerCardInformation(new CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
                    ->withBeAbleToExchangeToCash(false)
                    ->withPayableRuleWithCard(PayableLocaleRule::ONLY_ALLOWED_KOR)
                ;
        // @formatter:on

        $t = $builder->generateTokenBy("가맹점에게 전달한 비밀키");
        $token = SyrupPayTokenBuilder::verify($t, "가맹점에게 전달한 비밀키");
    }

    public function test_토큰_시럽페이사용자연동_추가_후_복호화()
    {
        $builder = new SyrupPayTokenBuilder();

        // @formatter:off
        $t = $builder->of("가맹점")
                    ->login()
                        ->withMerchantUserId("가맹점의 회원 ID 또는 식별자")
                        ->withExtraMerchantUserId("핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력")
                        ->withSsoCredential("SSO 를 발급 받았을 경우 입력")
                    ->next()
                    ->mapToSyrupPayUser()
                        ->withType(MappingType::CI_HASH)
                        ->withValue("asdkfjhsakdfj")
                    ->next()
                    ->generateTokenBy("가맹점에게 전달한 비밀키");
        // @formatter:on

        // When
        $token = SyrupPayTokenBuilder::verify($t, "가맹점에게 전달한 비밀키");

        // Then
        $this->assertNotNull($token);
        $this->assertTrue($token->isValidInTime());
        $this->assertEquals("가맹점", $token->getIss());
        $this->assertNotNull($token->getUserInfoMapper());
        $this->assertEquals('asdkfjhsakdfj', $token->getUserInfoMapper()->getMappingValue());
    }
}
