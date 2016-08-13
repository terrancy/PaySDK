<?php

class YSdk{
    private $dirYSdk;
    private $ts;
    private $dataYSdkConfig;
    private $ipUser='';
    private $sdk;

    function __construct(){
        $this->dirYSdk = dirname(__FILE__)."/YSDK";
        $this->getConfigInit();
        $this->getDataInit();
    }

    function getConfigInit(){
        require_once $this->dirYSdk."/Api.php";
        require_once $this->dirYSdk."/Payments.php";
        require_once $this->dirYSdk."/Ysdk.php";
    }

    function getDataInit(){
        $this->ts = time();
        require_once $this->dirYSdk."/configYSdk.php";
        $this->dataYSdkConfig = new configYSdk();

        // 创建YSDK实例
        $this->sdk = new Api($this->dataYSdkConfig->appid, $this->dataYSdkConfig->appkey);
        // 设置支付信息
        $this->sdk->setPay($this->dataYSdkConfig->pay_appid, $this->dataYSdkConfig->pay_appkey);
        // 设置YSDK调用环境
        $this->sdk->setServerName($this->dataYSdkConfig->server_name);
    }


    function tokenCheckByQQ($arrData){
        $params = array(
            'appid' => $arrData['appid'],
            'openid' => $arrData['openid'],
            'openkey' => $arrData['openkey'],
            'userip' => $this->ipUser,
            'sig' =>   md5($this->dataYSdkConfig->appkey.$this->ts),
            'timestamp' => $this->ts,
        );
        $ret = qq_check_token($this->sdk, $params);
        return $ret;
    }

    function tokenCheckByWeChat($arrData){
        $params = array(
            'appid' => $arrData['appid'],
            'openid' => $arrData['openid'],
            'userip' => $this->ipUser,
            'sig' => md5($this->dataYSdkConfig->appkey.$this->ts),
            'access_token' => $arrData['access_token'],
            'timestamp' => $this->ts,
        );

        $ret = wx_check_token($this->sdk, $params);
        return $ret;
    }

    function getBalanceM($arrData){
        $params = array(
            'openid' => $arrData['openid'],
            'openkey' => $arrData['openkey'],
            'pay_token' => $arrData['pay_token'],
            'ts' => $this->ts,
            'pf' => $arrData['pf'],
            'pfkey' => $arrData['pfkey'],
            'zoneid' => $arrData['idZone'],
        );
        $typeAccount=$arrData['accout_type'];
        $ret = get_balance_m($this->sdk, $params,$typeAccount);
        return $ret;
    }


    function payM($arrData){
        $params = array(
            'openid' => $arrData['openid'],
            'openkey' => $arrData['openkey'],
            'pay_token' => $arrData['pay_token'],
            'ts' => $this->ts,
            'pf' => $arrData['pf'],
            'pfkey' => $arrData['pfkey'],
            'zoneid' => $arrData['idZone'],
            'amt' => $arrData['amt'],
            'billno'=>$arrData['billno']
        );
        $typeAccount=$arrData['accout_type'];
        $ret = pay_m($this->sdk, $params, $typeAccount);
        return $ret;
    }

    function PresentM($arrData){
        $idDiscount = '';
        $idGift = '';
        $timePresets = 50;
        $params = array(
            'openid' => $arrData['openid'],
            'openkey' => $arrData['openkey'],
            'pay_token' => $arrData['pay_token'],
            'ts' => $this->ts,
            'pf' => $arrData['pf'],
            'pfkey' => $arrData['pfkey'],
            'zoneid' => $arrData['idZone'],
            'discountid' => $idDiscount,
            'giftid' => $idGift,
            'presenttimes' => $timePresets,
        );
        $typeAccount=$arrData['accout_type'];
        $ret = present_m($this->sdk, $params, $typeAccount);
        return $ret;
    }

}