<?php

class sinaSDK{

    private $arrConfigSina;
    private $dirBaseSina;

    function __construct(){
        $this->dirBaseSina = dirname(__FILE__)."/sinaSDK";
        $this->getConfigInit();
    }

    function getConfigInit(){
        $this->arrConfigSina = require_once $this->dirBaseSina."/config.inc.php";
        require_once $this->dirBaseSina."/SngClient.class.php";
    }

    function checkSign($arrData){
        $arrConfigSina = $this->arrConfigSina;
        $secretApp = $arrConfigSina['secretApp'];
        $signGenerateBySina = self::buildRequestMysignByPayment($arrData,$secretApp);
        if(strcmp($signGenerateBySina,$arrData['signature'])){
            return "failure";
        }else{
            return "success";
        }
    }

    /**
     * 支付接口签名机制
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * return 签名结果字符串
     */
    public static function buildRequestMysignByPayment($params,$secret)
    {
        if (empty($params)) return FALSE;
        if (isset($params['signature'])) unset($params['signature']);
        //将所有待签名参数按参数名排序
        ksort($params);

        //把数组所有元素，按照“参数|参数值”的模式用“|”字符拼接成字符串，组成字符串A
        $str_A = '';
        foreach ($params as $key => $value)
        {
            $str_A .= sprintf('%s|%s|', $key, $value);
        }

        //将字符串A与appsecret，用英文竖杠进行连接, 得到字符串B
        $str_B = $str_A . $secret;

        //对字符串B取sha1值，得到字符串C，C就是所需要的签名
        $str_C = sha1($str_B);

        return $str_C;
    }


    /**
     * 用户信息接口签名机制
     * 生成签名结果
     * @param $params 已排序要签名的数组
     * @param $sina_secret 新浪游戏运营人员分配的登录密钥
     * return 签名结果字符串
     */
    public static function buildRequestMysignByQuery($params, $signature_key){
        if (!isset($params['appkey'])) return '';
        if (empty($signature_key)) return '';
        if (isset($params['signature'])) unset($params['signature']);
        //将所有待签名参数按参数名排序
        ksort($params);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，组成字符串A
        $str_A = self::_createLinkString($params);

        //将字符串A与signature_key，用英文竖杠进行连接, 得到字符串C
        $str_C = sprintf('%s|%s', $str_A, $signature_key);

        //对字符串C取md5值，得到字符串D，D就是所需要的签名
        $str_D = md5($str_C);

        return $str_D;
    }

    /**
     * 用户信息接口签名机制
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    private static function _createLinkString($para){
        $arg = '';
        while (list ($key, $val) = each ($para)) {
            $arg .= $key.'='.$val.'&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, -1);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

        return $arg;
    }

}