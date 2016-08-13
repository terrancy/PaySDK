<?php

class IOSSDK{
    private $arrConfigSDK;
    private $dirBaseSDK;
    private $paySDK;

    function __construct(){
        $this->dirBaseSDK = dirname(__FILE__)."/IOSSDK";
        $this->getConfigInit();
        $this->getIOSPayInit();
    }

    function getConfigInit(){
        $this->arrConfigSDK = require_once $this->dirBaseSDK."/config.inc.php";
    }

    function getIOSPayInit(){
        require_once $this->dirBaseSDK."/ios.class.php";
        $this->paySDK = new IOSPay();
    }

    function getPaySign($arrData,$ListUnset=array(),$ListUrlEncode=array()){

    }

    function verifyPaySign($arrData){
        if(!empty($arrData)){
            $tokenPay = $arrData['paytoken'];
            if(strlen($tokenPay) >= 20){
                $arrVerifyPayGetIfSandbox = json_decode($this->paySDK->curlByIOSPay($tokenPay),true);
                if($arrVerifyPayGetIfSandbox['status'] == '21007'){
                    //若status == 21007为沙盒测试,重新请求下
                    $arrVerifyPayGetIfSandbox = json_decode($this->paySDK->curlByIOSPay($tokenPay,1),true);
                }
            }
        }
        $arrRst = empty($arrVerifyPayGetIfSandbox) ? array() : $arrVerifyPayGetIfSandbox;
        return json_encode($arrRst);
    }

    function getSignature($arrData,$keyCP,$ListUnset=array(),$ListUrlEncode=array()){
    }


}