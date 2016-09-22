<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1000808
 * Date: 2016-02-05
 * Time: 오후 3:43
 */

namespace syruppay\token;


use syruppay\token\claims\CardInstallmentInformation;
use syruppay\token\claims\Currency;
use syruppay\token\claims\DeliveryRestriction;
use syruppay\token\claims\elements\AdditionalDiscount;
use syruppay\token\claims\elements\DeliveryType;
use syruppay\token\claims\elements\Error;
use syruppay\token\claims\elements\ErrorType;
use syruppay\token\claims\elements\Loyalty;
use syruppay\token\claims\elements\LoyaltyId;
use syruppay\token\claims\elements\Offer;
use syruppay\token\claims\elements\OfferType;
use syruppay\token\claims\elements\ProductDeliveryInfo;
use syruppay\token\claims\Language;
use syruppay\token\claims\MappingType;
use syruppay\token\claims\PayableLocaleRule;
use syruppay\token\claims\ShippingAddress;
use syruppay\token\jwt\SyrupPayToken;

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

    /**
     * @expectedException syruppay\token\AlreadyBuiltException
     */
    public function test_이미_생성한_토큰빌드를_재활용()
    {
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

    public function test_체크아웃을_이용한_인증_토큰_생성()
    {
        $deliveryInfo = Mocks::getDeliveryInfo();
        $deliveryInfo = array_values($deliveryInfo);

        $builder = new SyrupPayTokenBuilder();
        // @formatter:off
        $builder->of("가맹점")
                ->checkout()
                    ->withProductPrice(5000)
                    ->withOffers(Mocks::getOfferList())
                    ->withLoyalties(Mocks::getLoyalList())
                    ->withProductDeliveryInfo($deliveryInfo[0])
                    ->withShippingAddresses(Mocks::getShippingAddress())
                    ->withSubmallName("11번가")
                ;
        // @formatter:on
        $token = SyrupPayTokenBuilder::verify($builder->generateTokenBy("가맹점에게 전달한 비밀키"), "가맹점에게 전달한 비밀키");

        $this->assertNotNull($token->getCheckoutInfo());
        $this->assertEquals(5000, $token->getCheckoutInfo()->getProductPrice());
    }

//    public function test_하위버전_1_2_30_호환_테스트()
//    {
//        SyrupPayTokenBuilder::uncheckValidationOfToken();
//        $tokenHistories = new TokenHistories();
//        $t = SyrupPayTokenBuilder::verify($tokenHistories->VERSION_1_2_30->token, $tokenHistories->VERSION_1_2_30->key);
//        var_dump($t);
//    }

    public function test_C_샵버전_0_0_1_호환_테스트()
    {
        SyrupPayTokenBuilder::uncheckValidationOfToken();
        $tokenHistories = new TokenHistories();
        $t = SyrupPayTokenBuilder::verify($tokenHistories->C_SHARP_0_0_1->token, $tokenHistories->C_SHARP_0_0_1->key);
//        var_dump($t);
    }

//    public function test_라이브러리_적용_전_버전_11번가_테스트() {
//        SyrupPayTokenBuilder::uncheckValidationOfToken();
//        $tokenHistories = new TokenHistories();
//        $t = SyrupPayTokenBuilder::verify($tokenHistories->BEFORE_11ST->token, $tokenHistories->BEFORE_11ST->key);
//
//        $this->assertNotNull($t->getTransactionInfo()->getMerchantTransactionAuthenticatedId());
//        $this->assertNotNull($t->getTransactionInfo()->getPaymentRestrictions()->getCardIssuerRegion());
//    }

    public function test_하위버전_1_3_4_버전_CJOSHOPPING_테스트()
    {
        SyrupPayTokenBuilder::uncheckValidationOfToken();
        $tokenHistories = new TokenHistories();
        $t = SyrupPayTokenBuilder::verify($tokenHistories->VERSION_1_3_4_BY_CJOSHOPPING->token, $tokenHistories->VERSION_1_3_4_BY_CJOSHOPPING->key);
        $this->assertNotNull($t->getTransactionInfo()->getMerchantTransactionAuthenticatedId());
        $this->assertNotNull($t->getTransactionInfo()->getPaymentRestrictions()->getCardIssuerRegion());
    }

//    public function test_정기_자동_결제_규격_추가_테스트()
//    {
//        $builder = new SyrupPayTokenBuilder();
//        // @formatter:off
//        $builder->of("가맹점")
//                ->subscription()
//                ->fixed()
//                ->withShippingAddress(new ShippingAddress("zipcode", "address1", "address2", "city", "state", "KR"))
//                ->withSubscriptionStartDate(time() / 1000)
//                ->withSubscriptionFinishDate(time() / 1000 + 365 * 24 * 60 * 60)
//                ->withPaymentCycle(PaymentCycle::ONCE_A_MONTH)
//                ;
//        // @formatter:on
//        $token = SyrupPayTokenBuilder::verify($builder->generateTokenBy("가맹점에게 전달한 비밀키"), "가맹점에게 전달한 비밀키");
//
//        $this->assertNotNull($token->getSubscription());
//    }

//    public function test_체크아웃_잘못된_규격_테스트()
//    {
//        SyrupPayTokenBuilder::uncheckValidationOfToken();
//        $tokenHistories = new TokenHistories();
//        $t = SyrupPayTokenBuilder::verify($tokenHistories->VERSION_1_3_5_INVALID->token, $tokenHistories->VERSION_1_3_5_INVALID->key);
//        $this->assertNotNull($t->getTransactionInfo()->getMerchantTransactionAuthenticatedId());
//        $this->assertNotNull($t->getTransactionInfo()->getPaymentRestrictions()->getCardIssuerRegion());
//    }
}

class Mocks
{
    public static $offerList = array();
    public static $loyalList = array();
    public static $shippingAddressList = array();
    public static $productDeliveryInfoList = array();

    public static function getOfferList()
    {
        $offer = new Offer();
        self::$offerList[] = $offer->setId("Offer-01")->setName("기본할인")->setAmountOff(1000)->setUserSelectable(false)->setOrderApplied(1);
        $offer = new Offer();
        self::$offerList[] = $offer->setId("Offer-02")->setName("복수구매할인")->setAmountOff(500)->setUserSelectable(false)->setOrderApplied(2);
        $offer = new Offer();
        self::$offerList[] = $offer->setId("Offer-03")->setName("추가할인")->setAmountOff(300)->setUserSelectable(false)->setOrderApplied(3);
        $offer = new Offer();
        self::$offerList[] = $offer->setId("Offer-04")->setName("보너스할인")->setAmountOff(700)->setUserSelectable(false)->setOrderApplied(4);
        $offer = new Offer();
        self::$offerList[] = $offer->setId("Offer-05")->setName("임직원할인")->setAmountOff(100)->setUserSelectable(false)->setOrderApplied(5);
        $offer = new Offer();
        self::$offerList[] = $offer->setId("Offer-06")->setName("카드사할인")->setAmountOff(1000)->setUserSelectable(true)->setOrderApplied(6);
        $offer = new Offer();
        self::$offerList[] = $offer->setId("Offer-07")->setName("플러스쿠폰")->setAmountOff(500)->setUserSelectable(true)->setOrderApplied(7);
        $offer = new Offer();
        self::$offerList[] = $offer->setId("Offer-08")->setType(OfferType::DELIVERY_COUPON)->setName("배송비쿠폰")->setAmountOff(2500)->setUserSelectable(true)->setOrderApplied(8);

        return self::$offerList;
    }

    public static function getLoyalList()
    {
        $loyalty = new Loyalty();
        self::$loyalList[] = $loyalty->setIdBy(LoyaltyId::MILEAGE_OF_11ST)->setName("마일리지")->setSubscriberId("Loyalty-Sub-Id-02")->setBalance(10000)->setMaxApplicableAmt(3000)->setInitialAppliedAmt(500)->setOrderApplied(1);
        $loyalty = new Loyalty();
        self::$loyalList[] = $loyalty->setIdBy(LoyaltyId::T_MEMBERSHIP)->setName("T멤버쉽")->setSubscriberId("Loyalty-Sub-Id-03")->setBalance(10000)->setMaxApplicableAmt(3000)->setInitialAppliedAmt(500)->setOrderApplied(2);
        $loyalty = new Loyalty();
        self::$loyalList[] = $loyalty->setIdBy(LoyaltyId::POINT_OF_11ST)->setName("포인트")->setSubscriberId("Loyalty-Sub-Id-04")->setBalance(10000)->setMaxApplicableAmt(3000)->setInitialAppliedAmt(1000)->setOrderApplied(3);
        $loyalty = new Loyalty();
        self::$loyalList[] = $loyalty->setIdBy(LoyaltyId::OK_CASHBAG)->setName("OK캐쉬백")->setSubscriberId("Loyalty-Sub-Id-05")->setBalance(10000)->setMaxApplicableAmt(3000)->setInitialAppliedAmt(10)->setOrderApplied(4);

        $error = new Error();
        $error->setType(ErrorType::MAINTENACE)->setDescription("T멤버쉽이 정기점검중이므로 일시적으로 서비스를 이용할 수 없습니다-> 잠시 후에 다시 이용해 주세요->");

        $errorLoyalty = new Loyalty();
        $errorLoyalty->setIdBy(LoyaltyId::T_MEMBERSHIP)->setName("T멤버쉽-에러상황")->setSubscriberId("Loyalty-Sub-Id-06")->setBalance(10000)->setMaxApplicableAmt(3000)->setInitialAppliedAmt(1000)->setOrderApplied(5);
        $errorLoyalty->setError($error);

        self::$loyalList[] = $errorLoyalty;

        $additionalDiscount = new AdditionalDiscount();
        $additionalDiscount->setPercentOff(10)->setMaxApplicableAmt(500);

        $addDiscLoyalty = new Loyalty();
        $addDiscLoyalty->setIdBy(LoyaltyId::OK_CASHBAG)->setName("OK캐쉬백-추가할인")->setSubscriberId("Loyalty-Sub-Id-01")->setBalance(10000)->setMaxApplicableAmt(3000)->setInitialAppliedAmt(10)->setOrderApplied(6);
        $addDiscLoyalty->setAdditionalDiscount($additionalDiscount);
        self::$loyalList[] = $addDiscLoyalty;

        return self::$loyalList;
    }


    public static function getShippingAddress()
    {
        $shippingAddress = new ShippingAddress();
        self::$shippingAddressList[] = $shippingAddress->setId("Shipping-Address-01")->setName("회사")->setCountryCode("KR")->setZipCode("12345")->setMainAddress("경기도 성남시 분당구 판교로264")->setDetailAddress("더플래닛")->setCity("성남시")->setState("경기도")->setRecipientName("USER")->setRecipientPhoneNumber("01012341234")->setDeliveryRestriction(DeliveryRestriction::NOT_FAR_AWAY)->setDefaultDeliveryCost(2500)->setAdditionalDeliveryCost(0)->setOrderApplied(1);
        $shippingAddress = new ShippingAddress();
        self::$shippingAddressList[] = $shippingAddress->setId("Shipping-Address-02")->setName("집")->setCountryCode("KR")->setZipCode("12345")->setMainAddress("경기도 성남시 분당구 판교로123")->setDetailAddress("SK플래닛 2사옥")->setCity("성남시")->setState("경기도")->setRecipientName("USER")->setRecipientPhoneNumber("01012341234")->setDeliveryRestriction(DeliveryRestriction::NOT_FAR_AWAY)->setDefaultDeliveryCost(2500)->setAdditionalDeliveryCost(0)->setOrderApplied(2);
        $shippingAddress = new ShippingAddress();
        self::$shippingAddressList[] = $shippingAddress->setId("Shipping-Address-03")->setName("시골")->setCountryCode("KR")->setZipCode("56789")->setMainAddress("강원도 삼척시 산골면 시골읍")->setDetailAddress("판자집")->setCity("삼척")->setState("강원도")->setRecipientName("USER")->setRecipientPhoneNumber("01012341234")->setDeliveryRestriction(DeliveryRestriction::FAR_AWAY)->setDefaultDeliveryCost(2500)->setAdditionalDeliveryCost(2500)->setOrderApplied(3);
        $shippingAddress = new ShippingAddress();
        self::$shippingAddressList[] = $shippingAddress->setId("Shipping-Address-04")->setName("섬나라")->setCountryCode("KR")->setZipCode("98765")->setMainAddress("제주도 서귀포시 제주면 제주읍")->setDetailAddress("돌담집")->setCity("서귀포")->setState("제주도")->setRecipientName("USER")->setRecipientPhoneNumber("01012341234")->setDeliveryRestriction(DeliveryRestriction::FAR_FAR_AWAY)->setDefaultDeliveryCost(2500)->setAdditionalDeliveryCost(5000)->setOrderApplied(4);

        return self::$shippingAddressList;
    }

    public static function getDeliveryInfo()
    {
        $deliveryInfo = new ProductDeliveryInfo();
        self::$productDeliveryInfoList[] = $deliveryInfo->setDeliveryType(DeliveryType::PREPAID)->setDeliveryName("선결제")->setDefaultDeliveryCostApplied(true)->setAdditionalDeliveryCostApplied(true);
        $deliveryInfo = new ProductDeliveryInfo();
        self::$productDeliveryInfoList[] = $deliveryInfo->setDeliveryType(DeliveryType::FREE)->setDeliveryName("무료배송")->setDefaultDeliveryCostApplied(false)->setAdditionalDeliveryCostApplied(true);
        $deliveryInfo = new ProductDeliveryInfo();
        self::$productDeliveryInfoList[] = $deliveryInfo->setDeliveryType(DeliveryType::DIY)->setDeliveryName("방문수령")->setDefaultDeliveryCostApplied(false)->setAdditionalDeliveryCostApplied(false);
        $deliveryInfo = new ProductDeliveryInfo();
        self::$productDeliveryInfoList[] = $deliveryInfo->setDeliveryType(DeliveryType::QUICK)->setDeliveryName("퀵서비스")->setDefaultDeliveryCostApplied(false)->setAdditionalDeliveryCostApplied(false);
        $deliveryInfo = new ProductDeliveryInfo();
        self::$productDeliveryInfoList[] = $deliveryInfo->setDeliveryType(DeliveryType::PAYMENT_ON_DELIVERY)->setDeliveryName("착불")->setDefaultDeliveryCostApplied(false)->setAdditionalDeliveryCostApplied(true);

        return self::$productDeliveryInfoList;
    }
}

class TokenHistories
{
    public $VERSION_1_2_30;
    public $C_SHARP_0_0_1;
    public $BEFORE_11ST;
    public $ALL = array();
    public $VERSION_1_3_4_BY_CJOSHOPPING;
    public $VERSION_1_3_5_INVALID;

    public function __construct()
    {
        $this->VERSION_1_2_30 = new History("1.2.30", "G3aIW7hYmlTjag3FDc63OGLNWwvagVUU", "sktmall_s002", "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJza3RtYWxsX3MwMDIiLCJhdWQiOiJodHRwczovL3BheS5zeXJ1cC5jby5rciIsInR5cCI6Impvc2UiLCJpYXQiOjE0NDU5OTg2ODAsImV4cCI6MTQ0NTk5OTI4MCwibG9naW5JbmZvIjp7Im1jdFVzZXJJZCI6IjM5ODcxMjMxIiwiU1NPQ3JlZGVudGlhbCI6IiIsImRldmljZUlkZW50aWZpZXIiOiIifSwidXNlckluZm9NYXBwZXIiOnsibWFwcGluZ1R5cGUiOiJDSV9NQVBQRURfS0VZIiwibWFwcGluZ1ZhbHVlIjoiMzk4NzEyMzEifSwidHJhbnNhY3Rpb25JbmZvIjp7Im1jdFRyYW5zQXV0aElkIjoiMjAxNTEwMjgwMDkxNjE4IiwicGF5bWVudEluZm8iOnsicHJvZHVjdFRpdGxlIjoi66Gv642wIOu5vOu5vOuhnCDtmZTsnbTtirjsv6DtgqQgMzJnIDQw6rCRIDFCT1giLCJsYW5nIjoia28iLCJjdXJyZW5jeUNvZGUiOiJLUlciLCJwYXltZW50QW10IjoyOTAwMCwic2hpcHBpbmdBZGRyZXNzIjoiYTI6S1J8fHVua25vd258fHwiLCJpc0V4Y2hhbmdlYWJsZSI6bnVsbCwiZGVsaXZlcnlOYW1lIjoiIiwiZGVsaXZlcnlQaG9uZU51bWJlciI6IiJ9LCJwYXltZW50UmVzdHJpY3Rpb25zIjp7ImNhcmRJc3N1ZXJSZWdpb24iOiJBTExPV0VEOmtvciJ9fSwiY2hlY2tvdXRJbmZvIjp7InByb2R1Y3RQcmljZSI6MjkwMDAsInN1Ym1hbGxOYW1lIjoiWyjso7wp7KCc7J207JWM7JSo7JeU7JeQ7IqkXSIsInByb2R1Y3REZWxpdmVyeUluZm8iOnsiZGVsaXZlcnlUeXBlIjoiRlJFRSIsImRlbGl2ZXJ5TmFtZSI6IuustOujjCIsImRlZmF1bHREZWxpdmVyeUNvc3RBcHBsaWVkIjpmYWxzZSwiYWRkaXRpb25hbERlbGl2ZXJ5Q29zdEFwcGxpZWQiOmZhbHNlLCJzaGlwcGluZ0FkZHJlc3NEaXNwbGF5Ijp0cnVlfSwib2ZmZXJMaXN0IjpbeyJpZCI6ImFkZEFtdFRvdCIsInVzZXJBY3Rpb25Db2RlIjpudWxsLCJ0eXBlIjpudWxsLCJuYW1lIjoi7LaU6rCA7ZWg7J24IiwiYW1vdW50T2ZmIjoxNDUwLCJ1c2VyU2VsZWN0YWJsZSI6ZmFsc2UsIm9yZGVyQXBwbGllZCI6M31dLCJsb3lhbHR5TGlzdCI6W3siaWQiOiJ1c2VPQ0IiLCJ1c2VyQWN0aW9uQ29kZSI6IlNQQTAzMDc6U1BBMDMxMSIsIm5hbWUiOiJPS-y6kOyJrOuwsSIsInN1YnNjcmliZXJJZCI6bnVsbCwiYmFsYW5jZSI6Mjc3ODE2LCJtYXhBcHBsaWNhYmxlQW10IjoyNzc4MTYsImV4Y2x1c2l2ZUdyb3VwSWQiOiIiLCJleGNsdXNpdmVHcm91cE5hbWUiOiIiLCJpbml0aWFsQXBwbGllZEFtdCI6Mjc1NTAsIm9yZGVyQXBwbGllZCI6NCwiYWRkaXRpb25hbERpc2NvdW50Ijp7InBlcmNlbnRPZmYiOjIwLjAsIm1heEFwcGxpY2FibGVBbXQiOjMwMDB9fV0sInNoaXBwaW5nQWRkcmVzc0xpc3QiOlt7ImlkIjoiM18wMDNfMCIsIm5hbWUiOiLsoJzso7zrj4Qo7YWM7Iqk7Yq4KSIsImNvdW50cnlDb2RlIjoia3IiLCJ6aXBDb2RlIjoiNjkwNzMyIiwic3RhdGUiOm51bGwsImNpdHkiOm51bGwsIm1haW5BZGRyZXNzIjoi7KCc7KO87Yq567OE7J6Q7LmY64-EIOygnOyjvOyLnCDsnbTrj4TsnbTrj5kgICDsoJzso7zrj4TspJHshozquLDsl4XsooXtlansp4Dsm5DshLzthLAgIiwiZGV0YWlsQWRkcmVzcyI6IjEx67KI7KeAIDEx64-ZIDEwMe2YuCIsInJlY2lwaWVudE5hbWUiOiLtl4jqt5wiLCJyZWNpcGllbnRQaG9uZU51bWJlciI6IjAxMDIyODk0MzY2IiwiZGVsaXZlcnlSZXN0cmljdGlvbiI6IkZBUl9GQVJfQVdBWSIsImRlZmF1bHREZWxpdmVyeUNvc3QiOjAsImFkZGl0aW9uYWxEZWxpdmVyeUNvc3QiOjMwMDAsIm9yZGVyQXBwbGllZCI6MX0seyJpZCI6IjFfMDAyXzAiLCJuYW1lIjoi7Jqw66as7KeRIiwiY291bnRyeUNvZGUiOiJrciIsInppcENvZGUiOiIxMzc3OTgiLCJzdGF0ZSI6bnVsbCwiY2l0eSI6bnVsbCwibWFpbkFkZHJlc3MiOiLshJzsmrjtirnrs4Tsi5wg7ISc7LSI6rWsIOyeoOybkOuPmSAgIO2VnOqwleyVhO2MjO2KuCAiLCJkZXRhaWxBZGRyZXNzIjoiNuuPmSA1MDjtmLgiLCJyZWNpcGllbnROYW1lIjoi7ZeI6recIiwicmVjaXBpZW50UGhvbmVOdW1iZXIiOiIwMTA5MDIyMTE3OSIsImRlbGl2ZXJ5UmVzdHJpY3Rpb24iOiJOT1RfRkFSX0FXQVkiLCJkZWZhdWx0RGVsaXZlcnlDb3N0IjowLCJhZGRpdGlvbmFsRGVsaXZlcnlDb3N0IjowLCJvcmRlckFwcGxpZWQiOjJ9LHsiaWQiOiIyXzAwMl8wIiwibmFtZSI6Iu2FjOyKpO2KuDIiLCJjb3VudHJ5Q29kZSI6ImtyIiwiemlwQ29kZSI6IjEzNzc5OCIsInN0YXRlIjpudWxsLCJjaXR5IjpudWxsLCJtYWluQWRkcmVzcyI6IuyEnOyauO2KueuzhOyLnCDshJzstIjqtawg7J6g7JuQ64-ZICAg7ZWc6rCV7JWE7YyM7Yq4ICIsImRldGFpbEFkZHJlc3MiOiIxMeuPmSAxMTExKO2FjOyKpO2KuCkiLCJyZWNpcGllbnROYW1lIjoi7ZeI6recIiwicmVjaXBpZW50UGhvbmVOdW1iZXIiOiIwMTAyMjg5NDM2NiIsImRlbGl2ZXJ5UmVzdHJpY3Rpb24iOiJOT1RfRkFSX0FXQVkiLCJkZWZhdWx0RGVsaXZlcnlDb3N0IjowLCJhZGRpdGlvbmFsRGVsaXZlcnlDb3N0IjowLCJvcmRlckFwcGxpZWQiOjN9XSwibW9udGhseUluc3RhbGxtZW50TGlzdCI6W3siY2FyZENvZGUiOiIwMSIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6IlsxMDAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjE7WVkxMjtZTjI7WU4zO1lONDtZWTU7WUg2O1lONztZTjg7WU45O1lOMTA7WU4xMSJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLTEwMDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lZNTtZSDY7WVkxODtZWTI0O1lOMjtZTjM7WU40O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xIn1dfSx7ImNhcmRDb2RlIjoiMDciLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJOTjE7Tlk1O05INjtOTjI7Tk4zO05ONDtOTjc7Tk44O05OOTtOTjEwO05OMTE7Tk4xMiJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMSJ9XX0seyJjYXJkQ29kZSI6IjAyIiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9XX0seyJjYXJkQ29kZSI6IjI3IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lZODtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjk7WU4xMDtZTjExO1lOMTIifSx7InBheW1lbnRBbXRSYW5nZSI6IlswLTUwMDAwKSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjEifV19LHsiY2FyZENvZGUiOiIzMSIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6IlswLTUwMDAwKSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjEifV19LHsiY2FyZENvZGUiOiIxNyIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6Ils4MDAwMC1dIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOWTM7Tk4yO05ONDtOTjU7Tkg2O05ONztOTjg7Tk45O05OMTA7Tk4xMTtOTjEyIn0seyJwYXltZW50QW10UmFuZ2UiOiJbNjAwMDAtODAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOSDY7Tk4yO05OMztOTjQ7Tk41O05ONztOTjg7Tk45O05OMTA7Tk4xMTtOTjEyIn0seyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiTk4xIn1dfSx7ImNhcmRDb2RlIjoiMDYiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiTk4xIn1dfSx7ImNhcmRDb2RlIjoiMzYiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xIn1dfSx7ImNhcmRDb2RlIjoiMDMiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjE7WVkxMjtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMSJ9XX0seyJjYXJkQ29kZSI6IjA0IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMSJ9XX0seyJjYXJkQ29kZSI6IjE2IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMSJ9XX0seyJjYXJkQ29kZSI6IjM1IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9XX0seyJjYXJkQ29kZSI6IjExIiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMSJ9XX0seyJjYXJkQ29kZSI6IjA4IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lZMjI7WU4yO1lOMztZTjQ7WU41O1lONjtZTjc7WU44O1lOOTtZTjEwO1lOMTE7WU4xMiJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9XX1dfX0.5Sv1IaiURo-isYMg0JFEKYrRdVQgE-PSdIkHoqFGSVA", "RS256 Header에 대한 예외처리 필요 버전, 추후 Deprecated");
        $this->C_SHARP_0_0_1 = new History("0.0.1", "1234567890123456", "syrup_order", "eyJhbGciOiJIUzI1NiIsImtpZCI6InN5cnVwX29yZGVyIiwidHlwIjoiSldUIn0.eyJhdWQiOiJodHRwczovL3BheS5zeXJ1cC5jby5rciIsInR5cCI6Impvc2UiLCJpc3MiOiJzeXJ1cF9vcmRlciIsImV4cCI6MTQ0NjYwNjY3NSwiaWF0IjoxNDQ2NjA2MDc1LCJqdGkiOiI1ZWVjZjFiYS05YTFhLTQwNDUtOWE4Yy03NDUzMDEzM2EyMWIiLCJuYmYiOjAsImxvZ2luSW5mbyI6eyJtY3RVc2VySWQiOiJzeXJ1cF9vcmRlcl91c2VyX2lkIiwiU1NPQ3JlZGVudGlhbCI6IlNTT19OTyIsImRldmljZUlkZW50aWZpZXIiOiIhISEhISEhISEhISEhISEhIn0sInVzZXJJbmZvTWFwcGVyIjp7Im1hcHBpbmdUeXBlIjoiQ0lfTUFQUEVEX0tFWSIsIm1hcHBpbmdWYWx1ZSI6IkNJIFZBTFVFIn0sInRyYW5zYWN0aW9uSW5mbyI6eyJtY3RUcmFuc0F1dGhJZCI6Ik9SRF8wMDAxIiwicGF5bWVudEluZm8iOnsiY2FyZEluZm9MaXN0IjpbeyJjYXJkQ29kZSI6Iuy5tOuTnOq1rOu2hCDsvZTrk5wiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoi7ZWg67aA7KCV67O0LiBleC4gTk4xO05OMjtZWTM7WVk0O1lZNTtOSDYifV0sInByb2R1Y3RUaXRsZSI6IuygnO2SiOuqhSIsInByb2R1Y3RVcmxzIjpbImh0dHA6Ly9kZWFsLjExc3QuY28ua3IvcHJvZHVjdC9TZWxsZXJQcm9kdWN0RGV0YWlsLnRtYWxsP21ldGhvZD1nZXRTZWxsZXJQcm9kdWN0RGV0YWlsJnByZE5vPTExMjI4NDEzNDAiLCJodHRwOi8vZGVhbC4xMXN0LmNvLmtyL3Byb2R1Y3QvU2VsbGVyUHJvZHVjdERldGFpbC50bWFsbD9tZXRob2Q9Z2V0U2VsbGVyUHJvZHVjdERldGFpbCZwcmRObz0xMjY1NTA4NzQxIl0sImxhbmciOiJLTyIsImN1cnJlbmN5Q29kZSI6IktSVyIsInBheW1lbnRBbXQiOjUwMDAwLCJzaGlwcGluZ0FkZHJlc3MiOiJrcnwxMzctMzMyfOyEnOy0iOq1rCDsnqDsm5Drj5kg7ZWY64KY7JWE7YyM7Yq4fDHrj5kgMe2YuHzshJzsmrh8fCIsImRlbGl2ZXJ5UGhvbmVOdW1iZXIiOiIwMTAxMTExMjIyMiIsImRlbGl2ZXJ5TmFtZSI6IuuwsOyGoSDsiJjsi6DsnpAiLCJpc0V4Y2hhbmdlYWJsZSI6ZmFsc2V9LCJwYXltZW50UmVzdHJpY3Rpb25zIjp7ImNhcmRJc3N1ZXJSZWdpb24iOiJOT1RfQUxMT1dFRDpVU0EifX19.lkQkO3INk6Cfb_cEYx36QT54hBtywa5vMAxwNgF-rb0", "C# 릴리즈 초기 버전");
        $this->BEFORE_11ST = new History("0.0.1", "8I9G4IPPqkik05WZj0Da3bJ5t7BEmXYj", "sktmall_s004", "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJza3RtYWxsX3MwMDQiLCJhdWQiOiJodHRwczovL3BheS5zeXJ1cC5jby5rciIsInR5cCI6Impvc2UiLCJpYXQiOjE0NDcyNzMyNDIsImV4cCI6MTQ0NzI3Mzg0MiwicGF5bWVudEluZm8iOnsicHJvZHVjdFRpdGxlIjoiW-2PieydvCA17IucIOuLueydvOy2nOqzoCEgJiMzODsg7Yag7JqU7J28IOy2nOqzoCEgJiMzODsg7ISx64Ko7KeA7JetIOuLueydvCDsp4HrsLDshqEhXSDslYTsnbTtj7A1L-yVhOydtO2PsDVTL-yVhOydtO2PsDYv7JWE7J207Y-wNisv6rCV7ZmU7Jyg66asL-yVoeygleuztO2YuC_slZ7rkqTrs7TtmLgv7ZW465Oc7Y-w7LyA7J207IqkL-uplO2DiOy8gOydtOyKpC_rr7jrn6zsvIDsnbTsiqQv7ZWY7Jqw7KeVL-2MjOyKpO2FlOy8gOydtOyKpCIsImxhbmciOiJrbyIsImN1cnJlbmN5Q29kZSI6IktSVyIsInBheW1lbnRBbXQiOiI1MzIwMCIsInNoaXBwaW5nQWRkcmVzcyI6ImEyOktSfHzshJzsmrjtirnrs4Tsi5wg6rWs66Gc6rWsIOyYpOulmDLrj5kgICDrjIDsmrDtkbjrpbTsp4DsmKTslYTtjIztirggfDEwNS0xMDAyfHwiLCJpc0V4Y2hhbmdlYWJsZSI6bnVsbCwiZGVsaXZlcnlOYW1lIjoi6rO97IiZ7Z2sIiwiZGVsaXZlcnlQaG9uZU51bWJlciI6IjAxMDkxNjU4NzQzIiwiY2FyZEluZm9MaXN0IjpbeyJjYXJkQ29kZSI6IjA4IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZWTI7WVkzO1lZNDtZWTU7WVk2O1lZNztZWTg7WVk5O1lZMTA7WVkxMTtZWTEyIn0seyJjYXJkQ29kZSI6IjI3IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZWTI7WVkzO1lZNDtZWTU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjIwIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjIxIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjIyIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjMyIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjMzIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjM3IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6Ijk3IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjE0IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjM0IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjAyIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZWTI7WVkzO1lZNDtZWTU7WVk2O1lZNztZWTg7WVk5O1lZMTA7WVkxMTtZWTEyIn0seyJjYXJkQ29kZSI6IjAxIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZWTI7WVkzO1lZNDtZWTU7WVk2O1lZNztZWTg7WVk5O1lZMTA7WVkxMTtZWTEyIn0seyJjYXJkQ29kZSI6IjE3IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOWTI7TlkzO05ZNDtOWTU7Tkg2O05ONztOTjg7Tk45O05IMTA7Tk4xMTtOTjEyIn0seyJjYXJkQ29kZSI6IjA0IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOWTI7TlkzO05ZNDtOWTU7Tkg2O05ONztOTjg7Tk45O05IMTA7Tk4xMTtOTjEyIn0seyJjYXJkQ29kZSI6IjA3IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZWTI7WVkzO1lZNDtZWTU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjE2IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOWTI7TlkzO05ZNDtOWTU7Tlk2O05ZNztOWTg7Tlk5O05ZMTA7TlkxMTtOWTEyIn0seyJjYXJkQ29kZSI6IjExIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOWTI7TlkzO05ZNDtOWTU7Tlk2O05ZNztOWTg7Tlk5O05ZMTA7TlkxMTtOWTEyIn0seyJjYXJkQ29kZSI6IjM1IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZWTI7WVkzO1lZNDtZWTU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjA2IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOWTI7TlkzO05ZNDtOWTU7Tkg2O05ONztOTjg7Tk45O05IMTA7Tk4xMTtOTjEyIn0seyJjYXJkQ29kZSI6IjMxIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZWTI7WVkzO1lZNDtZWTU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn0seyJjYXJkQ29kZSI6IjM2IiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZWTI7WVkzO1lZNDtZWTU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn1dfSwicGF5bWVudFJlc3RyaWN0aW9ucyI6eyJjYXJkSXNzdWVyUmVnaW9uIjoiQUxMT1dFRDprb3IifSwibG9naW5JbmZvIjp7Im1jdFVzZXJJZCI6IjI3Mzg2NjkxIiwiU1NPQ3JlZGVudGlhbCI6InNlclBtanNXbUlCTlJJMlFTdXJ5U3FydkJiOEhvbmE4eWJhNVRxdXVqUGMifSwidHJhbnNhY3Rpb25JbmZvIjp7Im1jdFRyYW5zQXV0aElkIjoiMjAxNTExMTI2MjExNjI0In0sInVzZXJJbmZvTWFwcGVyIjp7Im1hcHBpbmdUeXBlIjoiQ0lfTUFQUEVEX0tFWSIsIm1hcHBpbmdWYWx1ZSI6IjI3Mzg2NjkxIn19._9J4nh7Q5bK-5-A3XtrYTqlCDsT3Myi3rwVfKHlNzgU", "라이브러리 적용되지 전 버전 토큰");
        $this->ALL = array($this->VERSION_1_2_30, $this->C_SHARP_0_0_1, $this->BEFORE_11ST);
        $this->VERSION_1_3_4_BY_CJOSHOPPING = new History("1.3.4", "Qga3TKWrZpK4aU76MFAsa9WaOu5ybfAs", "cjoshopping", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImNqb3Nob3BwaW5nIiwidmVyIjoiMS4zLjEifQ.eyJhdWQiOiJodHRwczovL3BheS5zeXJ1cC5jby5rciIsInR5cCI6Impvc2UiLCJpc3MiOiJjam9zaG9wcGluZyIsImV4cCI6MTQ1MTk2ODM2NCwiaWF0IjoxNDUxOTY3NzY0LCJqdGkiOiJhMDkzNjg5Zi1iMmY2LTQ3NDAtOTA3Zi0xMWFkYjc2YmNjNzYiLCJuYmYiOjAsImxvZ2luSW5mbyI6eyJtY3RVc2VySWQiOiIyMDE1MDQwMTEwMDUifSwidHJhbnNhY3Rpb25JbmZvIjp7Im1jdFRyYW5zQXV0aElkIjoiMjAxNjAxMDUwNDkxNzAiLCJwYXltZW50SW5mbyI6eyJjYXJkSW5mb0xpc3QiOltdLCJwcm9kdWN0VGl0bGUiOiLrqY3rj4Trpqwg7JmA7Jqw7J207YGs66aw6rWs6rCV7LKt6rKw7Yuw7IqIMeunpCIsInByb2R1Y3RVcmxzIjpbImh0dHA6Ly9tcS5jam1hbGwuY29tL20vaXRlbS8yODcyNDgzMT9jaG5fY2Q9NTAwMDEwMDIiXSwibGFuZyI6IktPIiwiY3VycmVuY3lDb2RlIjoiS1JXIiwicGF5bWVudEFtdCI6MzQwMCwiaXNFeGNoYW5nZWFibGUiOmZhbHNlfSwicGF5bWVudFJlc3RyaWN0aW9ucyI6eyJjYXJkSXNzdWVyUmVnaW9uIjoiQUxMT1dFRDpLT1IifX19.ICLPdomAqyR_IASNJJnPWj9__znNmVUwAoUNvRVgYPM", "JOSE 라이브러리 1.3.2 버전 적용 후 테스트 용도");
        $this->VERSION_1_3_5_INVALID = new History("1.3.5", "G3aIW7hYmlTjag3FDc63OGLNWwvagVUU", "sktmall_s002", "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJza3RtYWxsX3MwMDIiLCJhdWQiOiJodHRwczovL3BheS5zeXJ1cC5jby5rciIsInR5cCI6Impvc2UiLCJpYXQiOjE0NTM0MjM1MzMsImV4cCI6MTQ1MzQyNDEzMywibG9naW5JbmZvIjp7Im1jdFVzZXJJZCI6IjM4MjU2ODU2IiwiU1NPQ3JlZGVudGlhbCI6IllVVmNqYmJITTdYdG92X1ZMdEhtTGg3MkZzQnJRU1FUV0x5ZXdab21xS28iLCJkZXZpY2VJZGVudGlmaWVyIjoiWVVWY2piYkhNN1h0b3ZfVkx0SG1MaDcyRnNCclFTUVRXTHlld1pvbXFLbyJ9LCJ1c2VySW5mb01hcHBlciI6eyJtYXBwaW5nVHlwZSI6IkNJX01BUFBFRF9LRVkiLCJtYXBwaW5nVmFsdWUiOiIzODI1Njg1NiJ9LCJ0cmFuc2FjdGlvbkluZm8iOnsibWN0VHJhbnNBdXRoSWQiOiIyMDE2MDEyMjAxMDU1NTQiLCJwYXltZW50SW5mbyI6eyJwcm9kdWN0VGl0bGUiOiJRQeq5gOyaqeyErV_snbzrsJjsg4HtkohfMDEiLCJsYW5nIjoia28iLCJjdXJyZW5jeUNvZGUiOiJLUlciLCJwYXltZW50QW10IjoxMDAwMCwic2hpcHBpbmdBZGRyZXNzIjoiYTI6S1J8fHVua25vd258fHwiLCJpc0V4Y2hhbmdlYWJsZSI6bnVsbCwiZGVsaXZlcnlOYW1lIjoiIiwiZGVsaXZlcnlQaG9uZU51bWJlciI6IiIsInByb2R1Y3RVcmxzIjpbImh0dHA6Ly9tLjExc3QuY28ua3IvTVcvUHJvZHVjdC9wcm9kdWN0QmFzaWNJbmZvLnRtYWxsP3ByZE5vPTEyNDQ3MDMxODIiXX0sInBheW1lbnRSZXN0cmljdGlvbnMiOnsiY2FyZElzc3VlclJlZ2lvbiI6IkFMTE9XRUQ6a29yIn19LCJjaGVja291dEluZm8iOnsicHJvZHVjdFByaWNlIjoxMDAwMCwic3VibWFsbE5hbWUiOiJb7YyM7Yq466Gc7JaEXSIsIm1haW5TaGlwcGluZ0FkZHJlc3NTZXR0aW5nRGlzYWJsZWQiOmZhbHNlLCJwcm9kdWN0RGVsaXZlcnlJbmZvIjp7ImRlbGl2ZXJ5VHlwZSI6IkZSRUUiLCJkZWxpdmVyeU5hbWUiOiLrrLTro4zrsLDshqEiLCJkZWZhdWx0RGVsaXZlcnlDb3N0QXBwbGllZCI6ZmFsc2UsImFkZGl0aW9uYWxEZWxpdmVyeUNvc3RBcHBsaWVkIjpmYWxzZSwic2hpcHBpbmdBZGRyZXNzRGlzcGxheSI6dHJ1ZX0sIm9mZmVyTGlzdCI6W3siaWQiOiJjYXJkUHJlRHNjQW10VG90IiwidXNlckFjdGlvbkNvZGUiOiJTUEFZQTAzMDMiLCJ0eXBlIjpudWxsLCJuYW1lIjoi7Lm065Oc7LaU6rCA7ZWg7J24IiwiYW1vdW50T2ZmIjo1MDAsInVzZXJTZWxlY3RhYmxlIjp0cnVlLCJvcmRlckFwcGxpZWQiOjgsImV4Y2x1c2l2ZUdyb3VwSWQiOiJNeXdheUV2ZW50IiwiZXhjbHVzaXZlR3JvdXBOYW1lIjoi64K066eY64yA66GcIO2VoOyduCIsImFjY2VwdGVkIjpbeyJ0eXBlIjoiQ0FSRCIsImNvbmRpdGlvbnMiOlt7ImNhcmRDb2RlIjoiMjciLCJtaW5QYXltZW50QW10IjoxMH0seyJjYXJkQ29kZSI6IjA3IiwibWluUGF5bWVudEFtdCI6MTB9LHsiY2FyZENvZGUiOiIwOCIsIm1pblBheW1lbnRBbXQiOjEwfSx7ImNhcmRDb2RlIjoiMzEiLCJtaW5QYXltZW50QW10IjoxMH1dfV19XSwibG95YWx0eUxpc3QiOlt7ImlkIjoidXNlTWlsZWFnZSIsInVzZXJBY3Rpb25Db2RlIjoiU1BBWUEwMzA0OlNQQVlBMDMwOCIsIm5hbWUiOiLrp4jsnbzrpqzsp4AiLCJzdWJzY3JpYmVySWQiOm51bGwsImJhbGFuY2UiOjQ4OTgwLCJtYXhBcHBsaWNhYmxlQW10Ijo1MDAsImV4Y2x1c2l2ZUdyb3VwSWQiOiJNeXdheUV2ZW50IiwiZXhjbHVzaXZlR3JvdXBOYW1lIjoi64K066eY64yA66GcIO2VoOyduCIsImluaXRpYWxBcHBsaWVkQW10Ijo1MDAsIm9yZGVyQXBwbGllZCI6MX0seyJpZCI6InVzZU1hbGxwb2ludCIsInVzZXJBY3Rpb25Db2RlIjoiU1BBWUEwMzA2OlNQQVlBMDMxMCIsIm5hbWUiOiLtj6zsnbjtirgiLCJzdWJzY3JpYmVySWQiOm51bGwsImJhbGFuY2UiOjI4ODI4MCwibWF4QXBwbGljYWJsZUFtdCI6Mjg4MjgwLCJpbml0aWFsQXBwbGllZEFtdCI6OTQ5MCwib3JkZXJBcHBsaWVkIjozfSx7ImlkIjoidXNlT0NCIiwidXNlckFjdGlvbkNvZGUiOiJTUEFZQTAzMDc6U1BBWUEwMzExIiwibmFtZSI6Ik9L7LqQ7Ims67CxIiwic3Vic2NyaWJlcklkIjpudWxsLCJiYWxhbmNlIjo0MzQwMDAsIm1heEFwcGxpY2FibGVBbXQiOjQzNDAwMCwiaW5pdGlhbEFwcGxpZWRBbXQiOjAsIm9yZGVyQXBwbGllZCI6NCwiYWRkaXRpb25hbERpc2NvdW50Ijp7InBlcmNlbnRPZmYiOjEwLjAsIm1heEFwcGxpY2FibGVBbXQiOjB9fV0sInNoaXBwaW5nQWRkcmVzc0xpc3QiOlt7ImlkIjoiYmFzaWNfMDAxXzAiLCJuYW1lIjoi6riw67O467Cw7Iah7KeAIiwiY291bnRyeUNvZGUiOiJrciIsInppcENvZGUiOiI3OTk4MjEiLCJzdGF0ZSI6bnVsbCwiY2l0eSI6bnVsbCwibWFpbkFkZHJlc3MiOiLqsr3sg4HrtoHrj4Qg7Jq466aJ6rWwIOu2geuptCDrgpjrpqwx66asICIsImRldGFpbEFkZHJlc3MiOiLrj4TshJwg7IKw6rCEIOyjvOyGjCIsInJlY2lwaWVudE5hbWUiOiLsmrjrponrj4QiLCJyZWNpcGllbnRQaG9uZU51bWJlciI6IjAxMDg4ODg4ODg4IiwiZGVsaXZlcnlSZXN0cmljdGlvbiI6IkZBUl9BV0FZIiwiZGVmYXVsdERlbGl2ZXJ5Q29zdCI6MCwiYWRkaXRpb25hbERlbGl2ZXJ5Q29zdCI6MCwib3JkZXJBcHBsaWVkIjoxfSx7ImlkIjoicmVjZW50XzAwMV8wIiwibmFtZSI6Iuy1nOq3vOuwsOyGoeyngCIsImNvdW50cnlDb2RlIjoia3IiLCJ6aXBDb2RlIjoiNzk5ODIxIiwic3RhdGUiOm51bGwsImNpdHkiOm51bGwsIm1haW5BZGRyZXNzIjoi6rK97IOB67aB64-EIOyauOumieq1sCDrtoHrqbQg64KY66asMeumrCAiLCJkZXRhaWxBZGRyZXNzIjoi64-E7IScIOyCsOqwhCDso7zshowiLCJyZWNpcGllbnROYW1lIjoi7Jq466aJ64-EIiwicmVjaXBpZW50UGhvbmVOdW1iZXIiOiIwMTA4ODg4ODg4OCIsImRlbGl2ZXJ5UmVzdHJpY3Rpb24iOiJGQVJfQVdBWSIsImRlZmF1bHREZWxpdmVyeUNvc3QiOjAsImFkZGl0aW9uYWxEZWxpdmVyeUNvc3QiOjAsIm9yZGVyQXBwbGllZCI6Mn0seyJpZCI6IjFfMDIxXzAiLCJuYW1lIjoi7Lac6rOg7KeAMSIsImNvdW50cnlDb2RlIjoia3IiLCJ6aXBDb2RlIjoiNDMxODA1Iiwic3RhdGUiOm51bGwsImNpdHkiOm51bGwsIm1haW5BZGRyZXNzIjoi6rK96riw64-EIOyViOyWkeyLnCDrj5nslYjqtawg6rSA7JaRMeuPmSAiLCJkZXRhaWxBZGRyZXNzIjoiMTMyMTIzMTIiLCJyZWNpcGllbnROYW1lIjoi6rmA66e5IiwicmVjaXBpZW50UGhvbmVOdW1iZXIiOiIwMTA1MTg3NjcyOCIsImRlbGl2ZXJ5UmVzdHJpY3Rpb24iOiJOT1RfRkFSX0FXQVkiLCJkZWZhdWx0RGVsaXZlcnlDb3N0IjowLCJhZGRpdGlvbmFsRGVsaXZlcnlDb3N0IjowLCJvcmRlckFwcGxpZWQiOjN9LHsiaWQiOiIzXzAxMV81MDEzMDI1MzI3MTA0MDEwMDAwMDAwMDAxIiwibmFtZSI6IuygnOyjvCIsImNvdW50cnlDb2RlIjoia3IiLCJ6aXBDb2RlIjoiNjk5OTQ0Iiwic3RhdGUiOm51bGwsImNpdHkiOm51bGwsIm1haW5BZGRyZXNzIjoi7KCc7KO87Yq567OE7J6Q7LmY64-EIOyEnOq3gO2PrOyLnCDrgqjsm5DsnY0g64Ko7JyE64Ko7ISx66GcMjQ367KI6ri4IDY0ICIsImRldGFpbEFkZHJlc3MiOiLsoJzso7zrj4Qg7KO87IaMIiwicmVjaXBpZW50TmFtZSI6Iuq5gOunuSIsInJlY2lwaWVudFBob25lTnVtYmVyIjoiMDEwMTIxMzEyMzEiLCJkZWxpdmVyeVJlc3RyaWN0aW9uIjoiRkFSX0ZBUl9BV0FZIiwiZGVmYXVsdERlbGl2ZXJ5Q29zdCI6MCwiYWRkaXRpb25hbERlbGl2ZXJ5Q29zdCI6MCwib3JkZXJBcHBsaWVkIjo0fV0sIm1vbnRobHlJbnN0YWxsbWVudExpc3QiOlt7ImNhcmRDb2RlIjoiMDEiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjE7WU4yO1lIMztZWTQ7WU41O1lONjtZTjc7WU44O1lOOTtZTjEwO1lOMTE7WU4xMiJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9XX0seyJjYXJkQ29kZSI6Ijk3IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lOMjtZTjM7WU40O1lONTtZTjY7WU43O1lOODtZTjk7WU4xMDtZTjExO1lOMTIifV19LHsiY2FyZENvZGUiOiIwNyIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6IlswLTUwMDAwKSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjEifSx7InBheW1lbnRBbXRSYW5nZSI6Ils1MDAwMC1dIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn1dfSx7ImNhcmRDb2RlIjoiMDIiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjE7WVkyO1lOMztZTjQ7WU41O1lONjtZTjc7WU44O1lOOTtZTjEwO1lOMTE7WU4xMiJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9XX0seyJjYXJkQ29kZSI6IjIwIiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lOMjtZTjM7WU40O1lONTtZTjY7WU43O1lOODtZTjk7WU4xMDtZTjExO1lOMTIifV19LHsiY2FyZENvZGUiOiIyNyIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6IlswLTUwMDAwKSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjEifSx7InBheW1lbnRBbXRSYW5nZSI6Ils1MDAwMC1dIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn1dfSx7ImNhcmRDb2RlIjoiMzEiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xIn0seyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjE7WU4yO1lOMztZTjQ7WU41O1lONjtZTjc7WU44O1lOOTtZTjEwO1lOMTE7WU4xMiJ9XX0seyJjYXJkQ29kZSI6IjE0IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lOMjtZTjM7WU40O1lONTtZTjY7WU43O1lOODtZTjk7WU4xMDtZTjExO1lOMTIifV19LHsiY2FyZENvZGUiOiIxNyIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6IlswLTUwMDAwKSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJOTjEifSx7InBheW1lbnRBbXRSYW5nZSI6Ils1MDAwMC1dIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOTjI7Tk4zO05ONDtOTjU7Tk42O05ONztOTjg7Tk45O05OMTA7Tk4xMTtOTjEyIn1dfSx7ImNhcmRDb2RlIjoiMDYiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiTk4xIn0seyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJOTjE7Tk4yO05OMztOTjQ7Tk41O05ONjtOTjc7Tk44O05OOTtOTjEwO05OMTE7Tk4xMiJ9XX0seyJjYXJkQ29kZSI6IjM2IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lOMjtZTjM7WU40O1lONTtZTjY7WU43O1lOODtZTjk7WU4xMDtZTjExO1lOMTIifV19LHsiY2FyZENvZGUiOiIzMiIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6IlswLTUwMDAwKSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjEifSx7InBheW1lbnRBbXRSYW5nZSI6Ils1MDAwMC1dIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn1dfSx7ImNhcmRDb2RlIjoiMjEiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xIn0seyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjE7WU4yO1lOMztZTjQ7WU41O1lONjtZTjc7WU44O1lOOTtZTjEwO1lOMTE7WU4xMiJ9XX0seyJjYXJkQ29kZSI6IjAzIiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lOMjtZTjM7WU40O1lONTtZTjY7WU43O1lOODtZTjk7WU4xMDtZTjExO1lOMTIifV19LHsiY2FyZENvZGUiOiIwNCIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6IlswLTUwMDAwKSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJOTjEifSx7InBheW1lbnRBbXRSYW5nZSI6Ils1MDAwMC1dIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOTjI7Tk4zO05ONDtOTjU7Tk42O05ONztOTjg7Tk45O05OMTA7Tk4xMTtOTjEyIn1dfSx7ImNhcmRDb2RlIjoiMzUiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xIn0seyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjE7WU4yO1lOMztZTjQ7WU41O1lONjtZTjc7WU44O1lOOTtZTjEwO1lOMTE7WU4xMiJ9XX0seyJjYXJkQ29kZSI6IjE2IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMSJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiTk4xO05OMjtOTjM7Tk40O05ONTtOTjY7Tk43O05OODtOTjk7Tk4xMDtOTjExO05OMTIifV19LHsiY2FyZENvZGUiOiIzNyIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6IlswLTUwMDAwKSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjEifSx7InBheW1lbnRBbXRSYW5nZSI6Ils1MDAwMC1dIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMTtZTjI7WU4zO1lONDtZTjU7WU42O1lONztZTjg7WU45O1lOMTA7WU4xMTtZTjEyIn1dfSx7ImNhcmRDb2RlIjoiMjIiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xIn0seyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjE7WU4yO1lOMztZTjQ7WU41O1lONjtZTjc7WU44O1lOOTtZTjEwO1lOMTE7WU4xMiJ9XX0seyJjYXJkQ29kZSI6IjMzIiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lOMjtZTjM7WU40O1lONTtZTjY7WU43O1lOODtZTjk7WU4xMDtZTjExO1lOMTIifV19LHsiY2FyZENvZGUiOiIxMSIsImNvbmRpdGlvbnMiOlt7InBheW1lbnRBbXRSYW5nZSI6IlswLTUwMDAwKSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJOTjEifSx7InBheW1lbnRBbXRSYW5nZSI6Ils1MDAwMC1dIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ik5OMTtOTjI7Tk4zO05ONDtOTjU7Tk42O05ONztOTjg7Tk45O05OMTA7Tk4xMTtOTjEyIn1dfSx7ImNhcmRDb2RlIjoiMDgiLCJjb25kaXRpb25zIjpbeyJwYXltZW50QW10UmFuZ2UiOiJbMC01MDAwMCkiLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xIn0seyJwYXltZW50QW10UmFuZ2UiOiJbNTAwMDAtXSIsIm1vbnRobHlJbnN0YWxsbWVudEluZm8iOiJZTjE7WU4yO1lOMztZTjQ7WU41O1lONjtZTjc7WU44O1lOOTtZTjEwO1lOMTE7WU4xMiJ9XX0seyJjYXJkQ29kZSI6IjM0IiwiY29uZGl0aW9ucyI6W3sicGF5bWVudEFtdFJhbmdlIjoiWzAtNTAwMDApIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6IllOMSJ9LHsicGF5bWVudEFtdFJhbmdlIjoiWzUwMDAwLV0iLCJtb250aGx5SW5zdGFsbG1lbnRJbmZvIjoiWU4xO1lOMjtZTjM7WU40O1lONTtZTjY7WU43O1lOODtZTjk7WU4xMDtZTjExO1lOMTIifV19XX19.IjeB1boyJcn3PVdYoQ5df0KTuMyHlnMtEWV6O6H5Cs8", "11번가 1.3.5 버전");
    }
}

class History
{
    public $version;
    public $key;
    public $iss;
    public $token;
    public $contents;

    public function __construct($version, $key, $iss, $token, $contents)
    {
        $this->version = $version;
        $this->key = $key;
        $this->iss = $iss;
        $this->token = $token;
        $this->contents = $contents;
    }
}
