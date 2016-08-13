<?php
class vivoSDK{

    private $arrConfigSDK;
    private $dirBaseSDK;
    private $vivoPay;
    private $urlPay = "https://pay.vivo.com.cn/vcoin/trade";

    function __construct(){
        $this->dirBaseSDK = dirname(__FILE__)."/vivoSDK";
        $this->getConfigInit();
    }

    function getConfigInit(){
        $this->arrConfigSDK = require_once $this->dirBaseSDK."/config.inc.php";
        require_once $this->dirBaseSDK."/vivoPay.class.php";
        $this->vivoPay = new  vivoPay();
    }


    function getPaySign($arrData,$arrConfigSDK){
        $arrPay = array(
            "version"       =>  $arrConfigSDK['version'],
            "signMethod"       =>  $arrConfigSDK['signMethod'],

            "cpId"       =>  $arrConfigSDK['idCP'],
            "appId"       =>  $arrConfigSDK['idApp'],
            "cpOrderNumber"       =>  $arrData['idOrder'],
            "notifyUrl"       =>  $arrConfigSDK['urlNotify'],

            "orderTime"       =>  date("YmdHis"),
            "orderAmount"       =>  $arrData['price'],
            "orderTitle"       =>  urlencode($arrData['productName']),
            "orderDesc"       =>  urlencode($arrData['productDesc']),
            "extInfo"       =>  $arrData['ext'],
        );
        $keyCP = $arrConfigSDK['keyCP'];
        $arrUnset = array("signMethod","signature");
        $arrUrlEncode = array();
        $arrPay['signature'] = $this->getSignature($arrPay,$keyCP,$arrUnset,$arrUrlEncode);
//        $jsonPay = json_encode($arrPay);
        $jsonPay = http_build_query($arrPay,NULL,"&");
//        file_put_contents("vivoSDK.txt","jsonPay:".json_encode($arrPay).PHP_EOL,FILE_APPEND);
        return json_decode($this->vivoPay->curlByPost($jsonPay,$this->urlPay),true);
    }

    function verifyPaySign($arrData){
        $arrConfigSDK = $this->arrConfigSDK;
        $callbackPay = $this->getPaySign($arrData,$arrConfigSDK);
        return $callbackPay;
//        if($callbackPay['respCode'] == 200){
//            $arrConfigSDK = $this->arrConfigSDK;
//            $signature = $this->getSignature($callbackPay,$arrConfigSDK['keyCP']);
//            if($signature == $callbackPay['signature']){
//                $arrRst = $callbackPay;
//            }
//        }
//        return empty($arrRst) ? $callbackPay : $arrRst;
    }

    function checkNotifyByViVo($arrData){
        if(!empty($arrData) && is_array($arrData)){
            $arrConfigSDK = $this->arrConfigSDK;
            $arrUnset = array("signMethod","signature");
            $arrUrlEncode = array();
            $signature = $this->getSignature($arrData,$arrConfigSDK['keyCP'],$arrUnset,$arrUrlEncode);
        }
        return empty($signature) ? false : strcmp($arrData['signature'],$signature) ? false : true;
    }

    function getSignature($arrData,$keyCP,$arrUnset,$arrUrlEncode){
        $signature = "";
        if(!empty($arrData) && is_array($arrData)){
            $strSignature = "";
            if(!empty($arrUnset) && is_array($arrUnset)){
                foreach($arrUnset as $valueUnset){
                    if(isset($arrData[$valueUnset])) unset($arrData[$valueUnset]);
                }
            }
            ksort($arrData);
            foreach($arrData as $key => $value){
                if(!empty($value)) {
                    $comma = empty($strSignature) ? "" : "&";
                    $value = isset($arrUrlEncode[$key]) ? urlencode($value) : $value;
                    $strSignature .= $comma.$key."=".$value;
                }
            }
            $strSignature = empty($strSignature) ? "" : $strSignature."&".strtolower(md5($keyCP));
//            file_put_contents("vivoSDK.txt","strSignature:".$strSignature.PHP_EOL,FILE_APPEND);
            $signature = empty($strSignature) ? "" : strtolower(md5($strSignature));
        }
        return $signature;
    }
}