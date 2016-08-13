<?php

class PaPaSDK{
    private $arrConfigSDK;
    private $dirBaseSDK;

    function __construct(){
        $this->dirBaseSDK = dirname(__FILE__)."/PaPaSDK";
        $this->getConfigInit();
    }

    function getConfigInit(){
        $this->arrConfigSDK = require_once $this->dirBaseSDK."/config.inc.php";
    }

    function getPaySign($arrData,$ListUnset=array(),$ListUrlEncode=array()){
        $arrConfigSDK = $this->arrConfigSDK;
        $keySecret = $arrConfigSDK['keyApp'].$arrConfigSDK['keySecret'];
        $signature = $this->getSignature($arrData,$keySecret,$ListUnset,$ListUrlEncode);
        return md5($signature);
    }

    function verifyPaySign($arrData){
        $signature = $this->getPaySign($arrData,array('sign'),array());
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
            $signature = $keyCP.$strSignature;
        }
        return $signature;
    }
}