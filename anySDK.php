<?php

class anySDK {
/*
String appKey = "38CFB482-416D-C1CD-B725-7AFC46476F0E";
String appSecret = "e7eef6079c5c6fda3df840e578213adc";
String privateKey = "1ADA80ADB24697D08E2D729553E57E0F";
*/
    private $keyPrivate="1ADA80ADB24697D08E2D729553E57E0F";
    private $keyEnhance ="MGZkOWIwMDU4ZGQ4Nzc5NTczYmU";
    private $dirLog = "";

    function __construct($keyPrivate="",$keyEnhance=""){
        if(!empty($keyPrivate) && !empty($keyEnhance)){
            $this->keyPrivate = $keyPrivate;
            $this->keyEnhance = $keyEnhance;
        }
        $this->getLogInit();
    }

    function getLogInit(){
        if($this->dirLog == ""){
            $this->dirLog = dirname(__FILE__)."/log/anySDK.txt";
        }
    }

    function payAnySDK($data){
//        file_put_contents($this->dirLog,date('Y-m-d H:i:s')."(data:):".json_encode($data).PHP_EOL,FILE_APPEND);
        if(($this->checkSign($data,$this->keyPrivate)) && ($this->checkEnhancedSign($data,$this->keyEnhance))){
            $arrRst = true;
        }else{
            $arrRst = false;
        }
        return $arrRst;
    }

    /**
     * 验签
     * @param array $data 接收到的所有请求参数数组，通过$_POST可以获得。注意data数据如果服务器没有自动解析，请做一次urldecode(参考rfc1738标准)处理
     * @param array $privateKey AnySDK分配的游戏privateKey
     * @return bool
     */
    function checkSign($data, $privateKey) {
        if (empty($data) || !isset($data['sign']) || empty($privateKey)) {
            return false;
        }
        $sign = $data['sign'];
        $_sign = $this->getSign($data, $privateKey);
//        file_put_contents($this->dirLog,date('Y-m-d H:i:s')."(_sign):".$_sign.PHP_EOL,FILE_APPEND);
        if ($_sign != $sign) {
            return false;
        }
        return true;
    }

    /**
     * 增强验签
     * @param type $data
     * @param type $enhancedKey
     * @return boolean
     */
    function checkEnhancedSign($data, $enhancedKey) {
        if (empty($data) || !isset($data['enhanced_sign']) || empty($enhancedKey)) {
            return false;
        }
        $enhancedSign = $data['enhanced_sign'];
        //sign及enhanced_sign 不参与签名
        unset($data['sign'], $data['enhanced_sign']);
        $_enhancedSign = $this->getSign($data, $enhancedKey);
//        file_put_contents($this->dirLog,date('Y-m-d H:i:s')."(_enhancedSign):".$_enhancedSign.PHP_EOL,FILE_APPEND);
        if ($_enhancedSign != $enhancedSign) {
            return false;
        }
        return true;
    }

    /**
     * 计算签名
     * @param array $data
     * @param string $privateKey
     * @return string
     */
    function getSign($data, $privateKey) {
        //sign 不参与签名
        unset($data['sign']);
        //数组按key升序排序
        ksort($data);
        //将数组中的值不加任何分隔符合并成字符串
        $string = implode('', $data);
        //做一次md5并转换成小写，末尾追加游戏的privateKey，最后再次做md5并转换成小写
        return strtolower(md5(strtolower(md5($string)) . $privateKey));
    }
}