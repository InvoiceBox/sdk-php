<?php

namespace Invoicebox\V2;

use Invoicebox\Exceptions\ApiNotConfiguredException;

final class Api
{
    
    const PRODUCTION_FORM = 'https://go.invoicebox.ru/module_inbox_auto.u';
    const TEST_FORM = 'https://go-dev.invoicebox.ru/module_inbox_auto.u';
    const BASE_URL = 'https://wss.invoicebox.ru/ws/participant/wsparticipantlite-pponly.u';


    private static $isConfigured = false;
    private static $baseUrl;
    private static $shopId;
    private static $shop_code;
    private static $key;
    private static $userAgent;
    private static $testEnvExists;
    private static $isTestRequest;
    private static $basic_auth_token;

    /**
     * @param array $accessConfig
     * @param bool $testEnvExists
     * @param bool $isTestRequest
     * @throws ApiNotConfiguredException
     */
    public static function configure(array $accessConfig, bool $testEnvExists=false, bool $isTestRequest=false)
    {
        if(!isset($accessConfig["shop_id"]) || empty($accessConfig["shop_id"])) throw new ApiNotConfiguredException("shop_id is required param");
        if(!isset($accessConfig["key"]) || empty($accessConfig["key"])) throw new ApiNotConfiguredException("key is required param");
        if(!isset($accessConfig["shop_code"]) || empty($accessConfig["shop_code"])) throw new ApiNotConfiguredException("shop_code is required param");
        if(!isset($accessConfig["user_agent"]) || empty($accessConfig["user_agent"])) {
            self::$userAgent = "PHP SDK/" . \Invoicebox\Invoicebox::SDK_VERSION;
        }
        if(isset($accessConfig["basic_auth_token"]) && !empty($accessConfig["basic_auth_token"])) self::$basic_auth_token = $accessConfig["basic_auth_token"];
        else{
            if(!isset($accessConfig["user"]) || empty($accessConfig["user"]) || !isset($accessConfig["password"]) || empty($accessConfig["password"])) throw new ApiNotConfiguredException("basic_auth_token or user and password is required params");
            self::$basic_auth_token = base64_encode($accessConfig["user"] . ":" . $accessConfig["password"]);
        }

        self::$shopId = $accessConfig["shop_id"];
        self::$shop_code = $accessConfig["shop_code"];
        self::$key = $accessConfig["key"];
        self::$testEnvExists = boolval($testEnvExists);
        self::$isTestRequest = boolval($isTestRequest);
        self::$baseUrl = self::BASE_URL;
        self::$isConfigured = true;
    }

    /**
     * @return bool
     * @throws ApiNotConfiguredException
     * @throws \Invoicebox\Exceptions\InvalidRequestException
     * @throws \Invoicebox\Exceptions\NotFoundException
     * @throws \Invoicebox\Exceptions\OperationErrorException
     */
    public static function isConfigured()
    {
        return self::$isConfigured;
    }
    
    public static function get($url, $args=[])
    {
        if(self::$isTestRequest) $args["itransfer_testmode"] = 1;
        $fullUrl     = self::$apiUrlBase . $url . '?' . http_build_query($args);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $fullUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'User-Agent:'.self::$userAgent,
            'Authorization: Basic ' . self::$basic_auth_token
        ));
        $result = self::executeAndDecode($curl);
        
        return $result;
    }
    
    public static function post($url, $args=[])
    {
        if ( ! self::$isConfigured) {
            throw new \Invoicebox\Exceptions\ApiNotConfiguredException();
        }

        if(self::$isTestRequest) $args["itransfer_testmode"] = 1;
        
        $json = json_encode($args);
        
        // fwrite(STDERR, print_r($fullArgs, true));
        $fullUrl = self::$baseUrl . $url;
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_URL, $fullUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json),
            'Accept:application/json',
            'User-Agent:'.self::$userAgent,
            'Authorization: Basic ' . self::$basic_auth_token
        ));
        $result = self::executeAndDecode($curl);
        
        return $result;
    }
    
    
    private static function executeAndDecode($curl)
    {
        $response = curl_exec($curl);
        $status   = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);


        $str_response = $response;
        if(is_string($response)) $response = json_decode($response, true);
        if(is_array($response) && isset($response["error"])) $code = $response["error"]["message"];
        else $code = "";
        
        if ($status == 401) {
            throw new \Invoicebox\Exceptions\ApiUnauthorizedException(strval($status) . $str_response);
        } elseif ($status == 404) {
            throw new \Invoicebox\Exceptions\NotFoundException(strval($status) . $str_response);
        } elseif ($status == 400) {
            throw new \Invoicebox\Exceptions\InvalidRequestException(strval($status) . $str_response);
        } elseif ($status != 200) {
            throw new \Invoicebox\Exceptions\OperationErrorException(strval($status) . $str_response);
        }
        
        return $response;
    }
    
}

?>
