<?php

class SouGouSDK{
    private $arrConfigSDK;
    private $dirBaseSDK;

    function __construct(){
        $this->dirBaseSDK = dirname(__FILE__)."/SouGouSDK";
        $this->getConfigInit();
    }

    function getConfigInit(){
        $this->arrConfigSDK = require_once $this->dirBaseSDK."/config.inc.php";
    }

    function getPaySign($arrData,$ListUnset=array(),$ListUrlEncode=array()){
        $arrConfigSDK = $this->arrConfigSDK;
        $keySecret = $arrConfigSDK['secretApp'];
        $signature = $this->getSignature($arrData,$keySecret,$ListUnset,$ListUrlEncode);
        return strtolower(md5($signature));
    }

    function verifyPaySign($arrData){
        $signature = $this->getPaySign($arrData,array('auth'),array());
        return empty($arrData) ? false : strcmp($arrData['auth'],$signature) ? false : true;
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
            $signature = $strSignature."&".$keyCP;
        }
        return $signature;
    }


}