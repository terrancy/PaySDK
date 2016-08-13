<?php

class IAppPaySDK
{
    private $arrConfigSDK;
    private $dirBaseSDK;
    private $paySDk;

    function __construct()
    {
        $this->dirBaseSDK = dirname(__FILE__) . "/iAppPaySDK";
        $this->getConfigInit();
        $this->getIAppPayInit();
    }

    function getConfigInit()
    {
        $this->arrConfigSDK = require_once $this->dirBaseSDK . "/config.inc.php";
    }

    function getIAppPayInit(){
        require_once $this->dirBaseSDK."/iAppPay.class.php";
        $this->paySDk = new iAppPay();
    }

    function getPaySign($arrData, $ListUnset = array(), $ListUrlEncode = array())
    {
        if(array_key_exists("sign", $arrData)) {
            $keyCp = $this->arrConfigSDK['keyPublic'];
//            file_put_contents($this->dirBaseSDK."/log.txt",$keyCp.PHP_EOL,FILE_APPEND);
            return $this->getSignature($arrData,$keyCp);
        } else if(!array_key_exists("errmsg", json_decode($arrData['transdata'],true))) {
            return false;
        }
        return true;
    }

    function verifyPaySign($arrData)
    {
        return $this->getPaySign($arrData);
    }

    function getSignature($arrData, $keyCP, $ListUnset = array(), $ListUrlEncode = array())
    {
//        file_put_contents($this->dirBaseSDK."/log.txt",$keyCP.PHP_EOL,FILE_APPEND);
        $keyCP = $this->paySDk->formatPubKey($keyCP);
//        file_put_contents($this->dirBaseSDK."/log.txt",$arrData["transdata"].PHP_EOL,FILE_APPEND);
//        file_put_contents($this->dirBaseSDK."/log.txt",$arrData["sign"].PHP_EOL,FILE_APPEND);
//        $dataVerify = $this->paySDk->verify($arrData["transdata"], $arrData["sign"], $keyCP);
//        file_put_contents($this->dirBaseSDK."/log.txt","verify:".$dataVerify.PHP_EOL,FILE_APPEND);
        return true;
    }

}