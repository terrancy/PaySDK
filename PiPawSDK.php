<?php

class PiPawSDK{
    private $arrConfigSDK;
    private $dirBaseSDK;

    function __construct(){
        $this->dirBaseSDK = dirname(__FILE__)."/PiPawSDK";
        $this->getConfigInit();
    }

    function getConfigInit(){
        $this->arrConfigSDK = require_once $this->dirBaseSDK."/config.inc.php";
    }

    function getPaySign($arrData,$ListUnset=array(),$ListUrlEncode=array()){
        $arrConfigSDK = $this->arrConfigSDK;
        $keySecret = $arrConfigSDK['keyPrivate'];
        $signature = $this->getSignature($arrData,$keySecret,$ListUnset,$ListUrlEncode);
        return strtolower(md5($signature));
    }

    function verifyPaySign($arrData){
        $signature = $this->getPaySign($arrData,array('sign','version'),array());
        return empty($arrData) ? false : strcmp($arrData['sign'],$signature) ? false : true;
    }

    function getSignature($arrData,$keyCP,$ListUnset=array(),$ListUrlEncode=array()){
        $signature = "";
        if(!empty($arrData) && is_array($arrData)){
            $strSignature = "";
            ksort($arrData);
            if(!empty($ListUnset) && is_array($ListUnset)){
                foreach($ListUnset as $valueUnset){
                    if(isset($arrData[$valueUnset])) unset($arrData[$valueUnset]);
                }
            }
            foreach($arrData as $key => $value){
                $comma = empty($strSignature) ? "" : "&";
                $value = isset($ListUrlEncode[$key]) ? base64_encode($value) : $value;
                $strSignature .= $comma.$key."=".$value;
            }
            $signature = $strSignature.$keyCP;
        }
        return $signature;
    }

    function getUserInfo($arrData){
        $arrConfigSDK = $this->arrConfigSDK;
        $arrGetUserInfo = array(
            "username"      =>      $arrData['username'],
            "appId"            =>      $arrConfigSDK['idApp'],
            "merchantId"            =>      $arrConfigSDK['idMerchant'],
            "merchantAppId"            =>      $arrConfigSDK['idAppMerchant'],
            "sid"            =>      $arrData['sid'],
            "time"            =>      $arrData['time'],
        );
        $jsonGetUserInfo = http_build_query($arrGetUserInfo,NULL,"&");
//        file_put_contents(dirname(__FILE__)."/login.txt",$jsonGetUserInfo.PHP_EOL,FILE_APPEND);
//        file_put_contents(dirname(__FILE__)."/login.txt",$arrConfigSDK['urlCheckSid'].PHP_EOL,FILE_APPEND);
        require_once $this->dirBaseSDK."/piPawPaw.class.php";
        $piPawPay = new piPawPay();
        return $piPawPay->curlByPost($jsonGetUserInfo,$arrConfigSDK['urlCheckSid']);
    }

}