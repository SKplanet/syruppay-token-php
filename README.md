## Overview
시럽페이 서비스에서 가맹점 인증 및 데이터 교환을 위한 규격을 정의하며 전송 구간에 대한 암호화 및 무결성 보장을 위한 토큰을 생성, 관리하는 기능을 수행한다.

### 개선 항목
1. JWT 규격 및 암,복호화에 대한 복잡도
2. 시럽페이 규격(도메인)에 대한 복잡도
3. 시럽페이 서비스 프로세스 구현에 대한 복잡도(Fluent API 지향)
4. 데이터 전송 구간 구현에 대한 복잡도

## Getting Start
## PHP version
=> PHP 5.2.0

## Installation
### composer ([packagist](https://packagist.org/packages/syruppay/token))
```
"syruppay/token": "v1.1.2"`

```

### 회원가입, 로그인, 설정과 같은 사용자 정보에 접근하기 위한 Syrup Pay Token 생성
회원가입, 설정, 로그인 기능 등과 같은 Syrup Pay 사용자 정보 접근하기 위해 사용되는 토큰을 설정하고 생성합니다.

##### Code
```php
// 사용자 로그인, 환경 설정 접근 시 
$builder = new syruppay_token_SyrupPayTokenBuilder();
$token = $builder->of("가맹점 ID")
    ->login()
    ->withMerchantUserId("가맹점의 회원 ID 또는 식별자")
    ->withExtraMerchantUserId("핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력")
    ->withSsoCredential("SSO 를 발급 받았을 경우 입력")
    ->next()
    ->generateTokenBy("가맹점에게 전달한 비밀키");
    
    
// 회원 가입 시
$builder = new syruppay_token_SyrupPayTokenBuilder();
$token = $builder->of("가맹점 ID")
    ->signUp()
    ->withMerchantUserId("가맹점의 회원 ID 또는 식별자")
    ->withExtraMerchantUserId("핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력")
    ->withSsoCredential("SSO 를 발급 받았을 경우 입력")
    ->next()
    ->generateTokenBy("가맹점에게 전달한 비밀키");
```

##### token의 결과
```language
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Ilx1YWMwMFx1YjlmOVx1YzgxMCBJRCJ9.eyJhdWQiOiJodHRwczpcL1wvcGF5LnN5cnVwLmNvLmtyIiwidHlwIjoiam9zZSIsImlzcyI6Ilx1YWMwMFx1YjlmOVx1YzgxMCBJRCIsImV4cCI6MTQ1NDczMTA0MiwiaWF0IjoxNDU0NzMwNDQyLCJqdGkiOiIyMGEzYzI3NC05YTIyLTQ3ZDUtYmUxMS03ZDJiZWYzOWVhMGMiLCJsb2dpbkluZm8iOnsibWN0VXNlcklkIjoiXHVhYzAwXHViOWY5XHVjODEwXHVjNzU4IFx1ZDY4Y1x1YzZkMCBJRCBcdWI2MTBcdWIyOTQgXHVjMmRkXHViY2M0XHVjNzkwIiwiZXh0cmFVc2VySWQiOiJcdWQ1NzhcdWI0ZGNcdWQzZjBcdWFjZmMgXHVhYzE5XHVjNzc0IFx1ZDY4Y1x1YzZkMCBcdWJjYzQgXHVjZDk0XHVhYzAwIElEIFx1Y2NiNFx1YWNjNFx1YWMwMCBcdWM4NzRcdWM3YWNcdWQ1NjAgXHVhY2JkXHVjNmIwIFx1Yzc4NVx1YjgyNSIsIlNTT0NyZWRlbnRpYWwiOiJTU08gXHViOTdjIFx1YmMxY1x1YWUwOSBcdWJjMWJcdWM1NThcdWM3NDQgXHVhY2JkXHVjNmIwIFx1Yzc4NVx1YjgyNSJ9fQ.b8nQakzSfoAA0PD6FmUbhASLZN3ZYi9M9hvV1AMn_Ow
```

##### token의 내용
```json
{
  "aud": "https://pay.syrup.co.kr",
  "typ": "jose",
  "iss": "가맹점 ID",
  "exp": 1454731042,
  "iat": 1454730442,
  "jti": "20a3c274-9a22-47d5-be11-7d2bef39ea0c",
  "loginInfo": {
    "mctUserId": "가맹점의 회원 ID 또는 식별자",
    "extraUserId": "핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력",
    "SSOCredential": "SSO 를 발급 받았을 경우 입력"
  }
}
```

### 결재 인증을 위한 Syrup Pay Token 생성
##### Code
```php
$builder = new syruppay_token_SyrupPayTokenBuilder();
$token = $builder->of("가맹점 ID")
    ->pay()
    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID")
    ->withProductTitle("제품명")
    ->withProductUrls(array(
        "http://deal.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1122841340",
        "http://deal.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1265508741"
    ))
    ->withLanguageForDisplay(LANGUAGE_KO)
    ->withAmount(50000)
    ->withCurrency(CURRENCY_KRW)
    ->withShippingAddress(new syruppay_token_claims_elements_ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "kr"))
    ->withDeliveryPhoneNumber("01011112222")
    ->withDeliveryName("배송 수신자")
    ->withInstallmentPerCardInformation(new syruppay_token_claims_elements_CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"))
    ->withBeAbleToExchangeToCash(false)
    ->withPayableRuleWithCard(PAYABLELOCALERULE_ONLY_ALLOWED_KOR)
    ->next()
    ->generateTokenBy("가맹점에게 전달한 비밀키");
```

##### token의 결과
```language
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Ilx1YWMwMFx1YjlmOVx1YzgxMCBJRCJ9.eyJhdWQiOiJodHRwczpcL1wvcGF5LnN5cnVwLmNvLmtyIiwidHlwIjoiam9zZSIsImlzcyI6Ilx1YWMwMFx1YjlmOVx1YzgxMCBJRCIsImV4cCI6MTQ1NDc0Mzk0NiwiaWF0IjoxNDU0NzQzMzQ2LCJqdGkiOiJkZGRiNjBjNS1mYjAxLTRlZjMtYmYyNi1hNzM1MDU4MzBmZDAiLCJ0cmFuc2FjdGlvbkluZm8iOnsibWN0VHJhbnNBdXRoSWQiOiJcdWFjMDBcdWI5ZjlcdWM4MTBcdWM1ZDBcdWMxMWMgXHVhZDAwXHViOWFjXHVkNTU4XHViMjk0IFx1YzhmY1x1YmIzOCBJRCIsInBheW1lbnRJbmZvIjp7ImNhcmRJbmZvTGlzdCI6W3siY2FyZENvZGUiOiJcdWNlNzRcdWI0ZGNcdWFkNmNcdWJkODQgXHVjZjU0XHViNGRjIiwibW9udGhseUluc3RhbGxtZW50SW5mbyI6Ilx1ZDU2MFx1YmQ4MFx1YzgxNVx1YmNmNC4gZXguIE5OMTtOTjI7WVkzO1lZNDtZWTU7Tkg2In1dLCJwcm9kdWN0VGl0bGUiOiJcdWM4MWNcdWQ0ODhcdWJhODUiLCJwcm9kdWN0VXJscyI6WyJodHRwOlwvXC9kZWFsLjExc3QuY28ua3JcL3Byb2R1Y3RcL1NlbGxlclByb2R1Y3REZXRhaWwudG1hbGw_bWV0aG9kPWdldFNlbGxlclByb2R1Y3REZXRhaWwmcHJkTm89MTEyMjg0MTM0MCIsImh0dHA6XC9cL2RlYWwuMTFzdC5jby5rclwvcHJvZHVjdFwvU2VsbGVyUHJvZHVjdERldGFpbC50bWFsbD9tZXRob2Q9Z2V0U2VsbGVyUHJvZHVjdERldGFpbCZwcmRObz0xMjY1NTA4NzQxIl0sImxhbmciOiJLTyIsImN1cnJlbmN5Q29kZSI6IktSVyIsInBheW1lbnRBbXQiOjUwMDAwLCJzaGlwcGluZ0FkZHJlc3MiOiJrcnwxMzctMzMyfFx1YzExY1x1Y2QwOFx1YWQ2YyBcdWM3YTBcdWM2ZDBcdWIzZDkgXHVkNTU4XHViMDk4XHVjNTQ0XHVkMzBjXHVkMmI4fDFcdWIzZDkgMVx1ZDYzOHxcdWMxMWNcdWM2Yjh8fCIsImRlbGl2ZXJ5UGhvbmVOdW1iZXIiOiIwMTAxMTExMjIyMiIsImRlbGl2ZXJ5TmFtZSI6Ilx1YmMzMFx1YzFhMSBcdWMyMThcdWMyZTBcdWM3OTAiLCJpc0V4Y2hhbmdlYWJsZSI6ZmFsc2V9LCJwYXltZW50UmVzdHJpY3Rpb25zIjp7ImNhcmRJc3N1ZXJSZWdpb24iOiJBTExPV0VEOktPUiJ9fX0.ysALJkS-BCACFmt__5CasVdSfldasq8uuwUEvkfEl5k
```

##### token의 내용
```json
{
  "aud": "https://pay.syrup.co.kr",
  "typ": "jose",
  "iss": "가맹점 ID",
  "exp": 1454743946,
  "iat": 1454743346,
  "jti": "dddb60c5-fb01-4ef3-bf26-a73505830fd0",
  "transactionInfo": {
    "mctTransAuthId": "가맹점에서 관리하는 주문 ID",
    "paymentInfo": {
      "cardInfoList": [
        {
          "cardCode": "카드구분 코드",
          "monthlyInstallmentInfo": "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6"
        }
      ],
      "productTitle": "제품명",
      "productUrls": [
        "http://deal.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1122841340",
        "http://deal.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1265508741"
      ],
      "lang": "KO",
      "currencyCode": "KRW",
      "paymentAmt": 50000,
      "shippingAddress": "kr|137-332|서초구 잠원동 하나아파트|1동 1호|서울||",
      "deliveryPhoneNumber": "01011112222",
      "deliveryName": "배송 수신자",
      "isExchangeable": false
    },
    "paymentRestrictions": {
      "cardIssuerRegion": "ALLOWED:KOR"
    }
  }
}
```

### 토큰 복호화
```php
$token = syruppay_token_SyrupPayTokenBuilder::verify("토큰", "가맹점에게 전달한 비밀키");
```
### 참고 사항
#### 이용하고자 하는 시럽페이 서비스 기능이 복합적인 경우 중첩하여 사용 가능하다.
##### 상황 1. 시럽페이 가입 여부를 모르는 상태에서 결제 하고자 하는 경우 (회원가입, 로그인, 결제 가능 토큰)
```php
$builder = new syruppay_token_SyrupPayTokenBuilder();
$token = $builder->of("가맹점 ID")
    ->signUp()
    ->withMerchantUserId("가맹점의 회원 ID 또는 식별자")
    ->withExtraMerchantUserId("핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력") // Optional
    ->next()
    ->pay()
    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID") // 가맹점 Transaction Id = mctTransAuthId
    ->withProductTitle("제품명")
    ->withProductUrls(array(
        "http://deal.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1122841340",
        "http://deal.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1265508741"
    )) // Optional
    ->withLanguageForDisplay(LANGUAGE_KO)
    ->withAmount(50000)
    ->withCurrency(CURRENCY_KRW)
    ->withShippingAddress(new syruppay_token_claims_elements_ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "kr")) // Optional
    ->withDeliveryPhoneNumber("01011112222") // Optional
    ->withDeliveryName("배송 수신자") // Optional
    ->withInstallmentPerCardInformation(new syruppay_token_claims_elements_CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6")) // Optional
    ->withBeAbleToExchangeToCash(false) // Optional
    ->withPayableRuleWithCard(PAYABLELOCALERULE_ONLY_ALLOWED_KOR) // Optional
    ->next()
    ->generateTokenBy("가맹점에게 전달한 비밀키");
```

##### 상황 2. 시럽페이에 자동 로그인 후 결제를 하고자 하는 경우 (자동 로그인, 결제 가능 토큰)
```php
$builder = new syruppay_token_SyrupPayTokenBuilder();
$token = $builder->of("가맹점 ID")
    ->login()
    ->withMerchantUserId("가맹점의 회원 ID 또는 식별자")
    ->withExtraMerchantUserId("핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력") // Optional
    ->withSsoCredential("발급 받은 SSO")
    ->next()
    ->pay()
    ->withOrderIdOfMerchant("가맹점에서 관리하는 주문 ID") // 가맹점 Transaction Id = mctTransAuthId
    ->withProductTitle("제품명")
    ->withProductUrls(array(
        "http://deal.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1122841340",
        "http://deal.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=1265508741"
    )) // Optional
    ->withLanguageForDisplay(LANGUAGE_KO)
    ->withAmount(50000)
    ->withCurrency(CURRENCY_KRW)
    ->withShippingAddress(new syruppay_token_claims_elements_ShippingAddress("137-332", "서초구 잠원동 하나아파트", "1동 1호", "서울", "", "kr")) // Optional
    ->withDeliveryPhoneNumber("01011112222") // Optional
    ->withDeliveryName("배송 수신자") // Optional
    ->withInstallmentPerCardInformation(new syruppay_token_claims_elements_CardInstallmentInformation("카드구분 코드", "할부정보. ex. NN1;NN2;YY3;YY4;YY5;NH6")) // Optional
    ->withBeAbleToExchangeToCash(false) // Optional
    ->withPayableRuleWithCard(PAYABLELOCALERULE_ONLY_ALLOWED_KOR) // Optional
    ->withMerchantDefinedValue('{"id_1":"value", "id_2":2}')    // Optional, 1k 제한
    ->next()
    ->generateTokenBy("가맹점에게 전달한 비밀키");
```

#### 4. 시럽페이에 자동 로그인 후 정기 결제 상품을 구매하고자 하는 경우(자동 로그인, 자동 정기 결제 가능 토큰)
##### - 준비중 -

## Extensional Function 
### 시럽페이 사용자 연동을 위한 Syrup Pay Token 세팅
Syrup Pay 사용자에 대한 정보를 조회하여 Syrup Pay 수동 로그인 시 ID 자동 입력과 같은 추가적인 기능을 수행할 수 있도록 매칭이 되는 정보를 설정하고 토큰을 생성합니다.
##### Java Code
```php
$builder = new syruppay_token_SyrupPayTokenBuilder();
$token = $builder->of("가맹점 ID")
    ->login()
    ->withMerchantUserId("가맹점의 회원 ID 또는 식별자")
    ->withExtraMerchantUserId("핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력")
    ->withSsoCredential("SSO 를 발급 받았을 경우 입력")
    ->next()
    ->mapToSyrupPayUser()
    ->withType(MAPPINGTYPE_CI_MAPPED_KEY)
    ->withValue("4987234")
    ->next()
    ->generateTokenBy("가맹점에게 전달한 비밀키");
```

##### token의 결과
```language
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6Ilx1YWMwMFx1YjlmOVx1YzgxMCBJRCJ9.eyJhdWQiOiJodHRwczpcL1wvcGF5LnN5cnVwLmNvLmtyIiwidHlwIjoiam9zZSIsImlzcyI6Ilx1YWMwMFx1YjlmOVx1YzgxMCBJRCIsImV4cCI6MTQ1NDc0NDkyNCwiaWF0IjoxNDU0NzQ0MzI0LCJqdGkiOiIzZjllYzM3ZS1mMTUwLTQ0YmUtYWVlYy04NzgyZDlkNTM3YjciLCJsb2dpbkluZm8iOnsibWN0VXNlcklkIjoiXHVhYzAwXHViOWY5XHVjODEwXHVjNzU4IFx1ZDY4Y1x1YzZkMCBJRCBcdWI2MTBcdWIyOTQgXHVjMmRkXHViY2M0XHVjNzkwIiwiZXh0cmFVc2VySWQiOiJcdWQ1NzhcdWI0ZGNcdWQzZjBcdWFjZmMgXHVhYzE5XHVjNzc0IFx1ZDY4Y1x1YzZkMCBcdWJjYzQgXHVjZDk0XHVhYzAwIElEIFx1Y2NiNFx1YWNjNFx1YWMwMCBcdWM4NzRcdWM3YWNcdWQ1NjAgXHVhY2JkXHVjNmIwIFx1Yzc4NVx1YjgyNSIsIlNTT0NyZWRlbnRpYWwiOiJTU08gXHViOTdjIFx1YmMxY1x1YWUwOSBcdWJjMWJcdWM1NThcdWM3NDQgXHVhY2JkXHVjNmIwIFx1Yzc4NVx1YjgyNSJ9LCJ1c2VySW5mb01hcHBlciI6eyJtYXBwaW5nVHlwZSI6IkNJX01BUFBFRF9LRVkiLCJtYXBwaW5nVmFsdWUiOiI0OTg3MjM0In19.DaBLUtyRzdRRDg7Z2lU0v65myvfuFD08qo-gz9UfeR0
```

##### token의 내용
```json
{
  "aud": "https://pay.syrup.co.kr",
  "typ": "jose",
  "iss": "가맹점 ID",
  "exp": 1454744924,
  "iat": 1454744324,
  "jti": "3f9ec37e-f150-44be-aeec-8782d9d537b7",
  "loginInfo": {
    "mctUserId": "가맹점의 회원 ID 또는 식별자",
    "extraUserId": "핸드폰과 같이 회원 별 추가 ID 체계가 존재할 경우 입력",
    "SSOCredential": "SSO 를 발급 받았을 경우 입력"
  },
  "userInfoMapper": {
    "mappingType": "CI_MAPPED_KEY",
    "mappingValue": "4987234"
  }
}
```

### 참고 사항
#### 이용하고자 하는 시럽페이 서비스 기능이 복합적인 경우 중첩하여 사용 가능하다.
##### 상황 1.
##### 상황 2.

## 시럽페이 체크아웃 기능 사용하기
가맹점의 쿠폰, 사용자 멤버쉽, 사용자의 배송지와 같은 주문 관련 정보와 기존 시럽페이의 간편 결제를 좀 더 편리하게(Seamless) 사용하기 위한 시럽페이의 확장된 기능   

### 주의
쿠폰(Offer)과 멤버쉽 포인트(Loyalty)에 대한 복합 결제를 지원한기 위한 기능으로 해당 서비스를 사용하기 위해서는 사전 협의 단계가 필요하다.

### 시럽페이 체크아웃을 이용하여 가맹점의 쿠폰(Offer)을 함께 결제 인증하기 위한 Syrup Pay Token 생성
##### - 준비중 -

### 시럽페이 체크아웃을 이용하여 멤버쉽 포인트(Loyalty)를 함께 결제 인증하기 위한 Syrup Pay Token 생성
##### - 준비중 -

### 시럽페이 체크아웃을 이용하여 배송지 정보를 멤버쉽 포인트(Loyalty)를 함께 결제 인증하기 위한 Syrup Pay Token 생성
##### - 준비중 -

### 주의
1. 한번 토큰을 생성한 SyrupPayTokenBuilder 를 재이용하여 다시 토큰을 빌드하거나 JSON 을 재성성 할 수 없다.
2. 각각의 토큰에 대한 내용(Claim)은 사용자 편의에 따라 입력 후 토큰을 생성할 수 있지만 Required 되는 필드에 대하여 미입력 시 토큰 생성 시점(SyrupPayTokenHandler#generateTokenBy(key) 호출 시점)에 InvalidArgumentException 을 throw 할 수 있다.
3. 토큰이 생성된 이후 유효시간은 **10분**으로 설정되어 있으며 이에 대한 수정이 필요할 경우 Syrup Pay 개발팀과 협의 후 제공하는 가이드를 따라야 한다. 

### 참고자료
1. JOSE RFC - [https://tools.ietf.org/wg/jose](https://tools.ietf.org/wg/jose/)
2. Syrup Pay JOSE - [https://github.com/skplanet/jose_php](https://github.com/skplanet/jose_php)
3. JWT IO - [http://jwt.io/](http://jwt.io/)
