<?php

class BaiDuMobileSDK{
    private $arrConfigSDK;
    private $dirBaseSDK;

    function __construct(){
        $this->dirBaseSDK = dirname(__FILE__)."/BaiDuSDK";
        $this->getConfigInit();
    }

    function getConfigInit(){
        $this->arrConfigSDK = require_once $this->dirBaseSDK."/config.inc.php";
    }

    function getPaySign($arrData,$ListUnset=array(),$ListUrlEncode=array()){
        $arrConfigSDK = $this->arrConfigSDK;
        $keySecret = $arrConfigSDK['keySecret'];
        return md5($this->getSignature($arrData,$keySecret,$ListUnset,$ListUrlEncode));
    }

    function verifyPaySign($arrData){
        if(!empty($arrData) && is_array($arrData)){
            $arrDataCallBack = array(
                "AppID"             =>      $arrData['AppID'],
                "ResultCode"        =>      0,
                "ResultMsg"        =>      "",
                "Sign"        =>      "",
                "Content"        =>      "",
            );
            $signMd5 = $this->getPaySign($arrData,array("Sign"),array("Content"));
            $codeResult = empty($arrData['Sign']) ? 0 : strcmp($arrData['Sign'],$signMd5) ? 0 : 1;
            $arrDataCallBack['ResultCode'] = $codeResult;
            $arrDataCallBack['ResultMsg'] = empty($codeResult) ? "失败" : "成功";
            $arrDataCallBack['Sign'] = $this->getPaySign($arrData,array("OrderSerial","CooperatorOrderSerial","Sign","Content"),array());
        }
        return empty($arrDataCallBack) ? array() : $arrDataCallBack;
    }

    function getSignature($arrData,$keyCP,$ListUnset=array(),$ListUrlEncode=array()){
        $signature = "";
        if(!empty($arrData) && is_array($arrData)){
            $strSignature = "";
            if(!empty($ListUnset) && is_array($ListUnset)){
                foreach($ListUnset as $valueUnset){
                    if(isset($arrData[$valueUnset])) unset($arrData[$valueUnset]);
                }
            }
            foreach($arrData as $key => $value){
                if(!empty($value)) {
                    $value = isset($ListUrlEncode[$key]) ? base64_encode($value) : $value;
                    $strSignature .= $value;
                }
            }
            $signature = $strSignature.$keyCP;
        }
        return $signature;
    }

}