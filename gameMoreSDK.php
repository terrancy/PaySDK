<?php

class gameMoreSDK{

    private $dirBasePay = "";
    private $arrConfigPay = "";
    private $gamePay;

    function __construct(){
        $this->dirBasePay = dirname(__FILE__)."/gameMoreSDK";
        $this->getConfigInit();
    }

    function getConfigInit(){
        $this->arrConfigPay = require_once $this->dirBasePay."/config.inc.php";
        require_once $this->dirBasePay."/gameMore.class.php";
        require_once $this->dirBasePay."/rsa.function.php";
        $this->gamePay = new  gameMorePay();
    }

    function getPaymentVerified($arrData){
        $arrConfigPay = $this->arrConfigPay;
        $deliveryCode = $this->getRsaDecryptByKeyPrivate($arrData['deliveryCode'],$arrConfigPay['keyPrivateRsa']);
        $arrVerify = array(
            "deliveryCode"      =>      $deliveryCode,
        );
//        $jsonVerify = json_encode($arrVerify);
        $jsonVerify = http_build_query($arrVerify,NULL,"&");
        $jsonVerifyCallBack = $this->gamePay->curlByPost($jsonVerify,$arrConfigPay['urlVerify']);
        $arrVerifyCallBack = json_decode($jsonVerifyCallBack,true);
        return $arrVerifyCallBack;
    }

    function getPaySign($arrData){
        return $this->getSignature($arrData);
    }

    function checkPaySign($arrData){
//        $arrConfigPay = $this->arrConfigPay;
//        $strSignature = $this->getPaySign($arrData);
//        $signatureEncrypt = $this->getRsaDecryptByKeyPrivate($arrData['sign'],$arrConfigPay['keyPrivateRsa']);
//        return $signatureEncrypt;
//        return strcmp($arrData['sign'],$signatureDecrypt) ? false : true;
        return true;
    }

    function getSignature($arrData){
        //去除sign
        unset($arrData['sign']);
        unset($arrData['deliveryCode']);
        unset($arrData['checkCode']);

        //排序
        ksort($arrData);

        $strSignature = "";
        if(!empty($arrData) && is_array($arrData)){
            $strSignature = implode("",$arrData);
        }

        return $strSignature;
    }

    /**
     * 说明:私钥解密
     */
    function getRsaDecryptByKeyPrivate($data,$keyPrivate){
        $keyPrivate = file_get_contents($keyPrivate);
        openssl_private_decrypt($this->urlSafe_base64_decode($data),$decrypted,$keyPrivate);
        return $decrypted;
    }

    /**
     * 说明:公钥加密
     */
    function getRsaEncryptByKeyPublic($data,$keyPublic){
        $keyPublic = file_get_contents($keyPublic);
        openssl_public_encrypt($data,$crypted,$keyPublic);
        return $this->urlSafe_base64_encode($crypted);
    }

    function urlSafe_base64_encode($str){
        $strFind = array("+","/");
        $strReplace = array("-","_");
        return str_replace($strFind,$strReplace,base64_encode($str));
    }

    function urlSafe_base64_decode($code){
        $strFind = array("+","/");
        $strReplace = array("-","_");
        return base64_decode(str_replace($strReplace,$strFind,$code));
    }
}