<?php

declare(strict_types=1);

$currencyCodeFrom = 'RUB';
$message = '';

$chatID = '@DexArtTestBot';
$botToken = '5425326076:AAEthrIwllwPwgjzC9plYUP3TCmIHuFx_4k';

if (array_key_exists ('currencyCode',$_POST)){ $currencyCodeTo =trim($_POST['currencyCode']);//expects currency code in ISO 4217
}else{
	$message = "No currency code (ISO 4217) sent! POST must include currencyCode key!";
	telegramBotSendCurrencyRate($chatID,$message,$botToken);
exit(1);
}

$fmt = numfmt_create( 'ru_RU', NumberFormatter::CURRENCY ); //check for ISO 4217
if(!numfmt_format_currency($fmt, 0, $currencyCodeTo)){
	$message = "Incorrect currency code (ISO 4217) sent! Your code was $currencyCodeTo.";
	telegramBotSendCurrencyRate($chatID,$message,$botToken);
	exit(2);
}


$message = "Currency rate for: $currencyCodeTo ".getCurrencyRate($currencyCodeFrom,$currencyCodeTo);

telegramBotSendCurrencyRate($chatID,$message,$botToken);
exit;

function getCurrencyRate(string $currencyCodeFrom, string $currencyCodeTo){

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.apilayer.com/exchangerates_data/convert?to=$currencyCodeFrom&from=$currencyCodeTo&amount=1",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: text/plain",
    "apikey: cZH3SfAUfYx11FmBCfeX6bdzfUF6P1IG"
  ),
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET"
));

$response = curl_exec($curl);
if (!curl_errno($curl)) {
  switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
    case 200:
      break;
    default:
      echo 'Unexpected HTTP code: ', $http_code, "\n";
	  die();
  }
}
curl_close($curl);
$decodedResponse = json_decode($response, true);


return $decodedResponse['info']['rate'];

}

function telegramBotSendCurrencyRate(string $chatID, string $message, string $token){

    $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatID;
    $url = $url . "&text=" . urlencode($message);
    $curl = curl_init();

    curl_setopt_array($curl, array(
	        CURLOPT_URL => $url,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true
			));
    $result = curl_exec($curl);
    curl_close($curl);
    return $result; //returns message if sent
}