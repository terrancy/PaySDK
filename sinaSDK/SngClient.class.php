<?php
/**
 * Sng Client example By sina
 * @example
 * require 'SngClient.class.php';
 * $appkey = 1283205126;
 * $access_token = '2.00YOrU9BsQMq5Bb03fd58bf20WqbCO';
 * //接口的签名密钥(新浪游戏运营人员分配)
 * $signkey = 'abcdefg';
 * //授权校验-获取是否校验成功
 * $ret = SngClient::singleton($appkey, $access_token)->userCheckIn($suid, $signkey);
 * var_dump ( $ret );
 *
 * //授权校验-生成签名串
 * $auth_signature = SngClient::singleton($appkey, $access_token)->getCheckInSign($suid, $signkey);
 * echo $auth_signature;
 *
 * //充值回调-生成签名串
 * $appkey = 1283205126;
 * $order_id = 'sng22CaE6u2f93G';
 * $amount = 1;
 * $order_uid = 3961261159;
 * $actual_amount = 1;
 * //游戏的APP_SECRET(微博开放平台分配)
 * $app_secret = 'abcd';
 * //CP游戏方的透传参数
 * $pt = '1234';
 * $pay_signature = SngClient::singleton($appkey, $access_token)->getPayNotifySign($order_id, $amount, $order_uid, $actual_amount, $app_secret, $pt);
 * echo $pay_signature;
 */

/**
 * 如果您的 PHP 没有安装 cURL 扩展，请先安装
 */
if (!function_exists('curl_init'))
{
    throw new Exception('SngClient Require the cURL PHP extension.');
}

/**
 * 如果您的 PHP 没有安装 json 扩展，请先安装
 */
if (! function_exists ( 'json_decode' )) {
    throw new Exception ( 'SngClient Require JSON extension' );
}

/**
 * Sng Client类
 *
 * @version 1.0
 */
class SngClient
{
    /**
     * sng http
     * @var object
     */
    private static $instance;

    /**
     * appkey
     *
     * @var string
     */
    protected $appkey = null;

    /**
     * access_token
     *
     * @var string
     */
    protected $access_token = null;

    /**
     * 构造函数
     *
     * @param string $appkey
     * @param string $access_token
     */
    private function __construct($appkey, $access_token, $debug = FALSE)
    {
        $this->appkey = $appkey;
        $this->access_token = $access_token;
        $this->sngHttp = new SngHttp();
        if ($debug)
        {
            $this->sngHttp->debug = true;
        }
    }

    public static function singleton($appkey, $access_token = '', $debug = FALSE)
    {
        if ( ! (self::$instance instanceof self ) )
        {
            self::$instance = new self($appkey, $access_token, $debug);
        }
        return self::$instance;
    }

    /**
     * 授权校验-获取是否校验成功
     *
     * @param int $suid	玩家的SUID
     * @param string $signkey	接口的签名密钥(新浪游戏运营人员分配)
     * @return boolean
     */
    public function userCheckIn($suid, $signkey, $deviceid = '')
    {
        if (empty($suid) OR empty($signkey) OR empty($this->appkey) OR empty($this->access_token))
        {
            sngHttp::outputError(10016, sprintf('missing required parameters: %s', 'suid|signkey|appkey|token'));
        }
        $params = array(
            'suid' => $suid,
            'appkey' => $this->appkey,
            'token' => $this->access_token,
            'deviceid' => $deviceid
        );
        $params['signature'] = sngHttp::makeSign($params, $signkey);
        $ret = $this->sngHttp->post( '/sdk/user/check', $params );
        return (!isset($ret['error_code'])) ? true : false;
    }

    /**
     * 授权校验-生成签名串
     *
     * @param int $suid	玩家的SUID
     * @param string $signkey	接口的签名密钥(新浪游戏运营人员分配)
     * @return string
     */
    public function getCheckInSign($suid, $signkey, $deviceid = '')
    {
        if (empty($suid) OR empty($signkey) OR empty($this->appkey) OR empty($this->access_token))
        {
            sngHttp::outputError(10016, sprintf('missing required parameters: %s', 'suid|signkey|appkey|token'));
        }
        $params = array(
            'suid' => $suid,
            'appkey' => $this->appkey,
            'token' => $this->access_token,
            'deviceid' => $deviceid
        );
        return sngHttp::makeSign($params, $signkey);
    }

    /**
     * 充值回调-生成签名串
     *
     * @param string $order_id		SNG的订单ID
     * @param int $amount			订单金额(单位:分)
     * @param int $order_uid		用户支付时的UID
     * @param int $actual_amount	用户实际支付金额(单位:分)
     * @param string $app_secret	游戏的APP_SECRET(微博开放平台分配)
     * @param string $pt			CP的附加参数
     * @return string
     */
    public function getPayNotifySign($order_id, $amount, $order_uid, $actual_amount, $app_secret, $pt = '')
    {
        if (empty($order_id) OR empty($amount) OR empty($order_uid) OR empty($actual_amount) OR empty($app_secret))
        {
            sngHttp::outputError(10016, sprintf('missing required parameters: %s', 'order_id|amount|order_uid|actual_amount|app_secret'));
        }
        $params = array(
            'order_id' => $order_id,
            'amount' => $amount, //单位:分
            'order_uid' => $order_uid,
            'source' => $this->appkey,
            'actual_amount' => $actual_amount //单位:分
        );
        if ($pt)
        {
            $params['pt'] = $pt;
        }
        //生成签名
        return sngHttp::makePaySign($params, $app_secret);
    }

    /**
     * SngClient can not be cloned
     *
     */
    private function __clone() {}

    /**
     * SngClient can not be serialized
     */
    private function __sleep() {}

    /**
     * SngClient can not be deserialized
     */
    private function __wakeup() {}
}

/**
 * Sng 发送HTTP网络请求类
 *
 */
class SngHttp
{
    /**
     * api url
     *
     * @var string
     */
    public $apiUrl = 'http://m.game.weibo.cn/api';

    /**
     * user agent
     *
     * @var string
     */
    protected $userAgent = 'wyx sng Agent Alpha 1.0';

    /**
     * sign key
     * @var string
     */
    protected $signKey = '';

    /**
     * format
     * @var string
     */
    protected $format  = 'json';

    /**
     * sdk platform
     *
     * @var string
     */
    protected $sdk_platform = 'a';

    /**
     * sdk version
     *
     * @var string
     */
    protected $sdk_version = '1.0';

    /**
     * sng sdk version
     *
     * @var string
     */
    protected $sng_sdk_version = '1.0.28';

    /**
     * set connect timeout
     *
     * @var int
     */
    protected $connectTimeout = 10;

    /**
     * set timeout
     *
     * @var int
     */
    protected $timeout = 10;

    /**
     * httpCode returned.
     *
     * @var int
     */
    protected $httpCode;

    /**
     * httpInfo returned.
     *
     * @var string
     */
    protected $httpInfo;

    /**
     * http content returned
     *
     * @var string
     */
    protected $content = '';

    /**
     * is post
     * @var boolen
     */
    protected $isPost = false;

    /**
     * isset header
     *
     * @var boolen
     */
    protected $header = false;

    /**
     * print the debug info
     *
     * @var boolen
     */
    public $debug = false;

    /**
     * 构造函数
     */
    public function __construct() {}

    /**
     * call api (post) 请求API(post方式)
     *
     * @param string $api  api name 接口名
     * @param array $data params except verify_code
     * @return array
     */
    public function post($api, $data = array()) {
        $this->isPost = true;
        if (strpos($api, '.'.$this->format) === false){
            $api .= '.'.$this->format;
        }
        return json_decode ( $this->http ( $this->apiUrl . $api, $this->buildQueryParamStr ( $data ), 1 ), true );
    }

    /**
     * call api (get) 请求API(get方式)
     *
     * @param string $api   name 接口名
     * @param array $data  params except verify_code
     * @return array
     */
    public function get($api, $data = array()) {
        if (strpos($api, '.'.$this->format) === false){
            $api .= '.'.$this->format;
        }
        $send_params = is_array($data) ? $this->buildQueryParamStrInit ( $data ) : $data;
        return json_decode ( $this->http ( $this->apiUrl . $api, $send_params, 0 ), true );
    }

    /**
     * set http header
     */
    protected function setHeaders() {
        $this->header = array (
            'sdk_version:' . $this->sdk_version,
            'sdk_platform:' . $this->sdk_platform,
            'sng_sdk_version:' . $this->sng_sdk_version,
        );
        return true;
    }

    public function addHeaders(&$data) {
        if (isset($data['headers']) && $data['headers'])
        {
            $this->header = array();
            foreach ($data['headers'] as $field => $value)
            {
                $this->addHeader($field, $value);
            }
        }
        return true;
    }

    public function addHeader($field, $value) {
        $this->header[] = $field . ':' . $value;
        return true;
    }

    /**
     * buildQueryParamStr
     *
     * @param array $data
     * @return string
     */
    public function buildQueryParamStr(&$data) {
        $baseString = self::buildBaseString ( $data );
        return $baseString;
    }

    /**
     * buildBaseString
     *
     * @param array $params
     * @return string
     */
    public static function buildBaseString(&$params) {
        if (! $params)
            return '';
        $keys = self::urlencodeRfc3986 ( array_keys ( $params ) );
        $values = self::urlencodeRfc3986 ( array_values ( $params ) );
        $params = array_combine ( $keys, $values );

        uksort ( $params, 'strcmp' );

        $pairs = array ();
        foreach ( $params as $parameter => $value ) {
            if (is_array ( $value )) {
                natsort ( $value );
                foreach ( $value as $duplicate_value ) {
                    $pairs [] = $parameter . '=' . $duplicate_value;
                }
            } else
            {
                $pairs [] = $parameter . '=' . $value;
            }
        }
        return implode ( '&', $pairs );
    }

    /**
     * urlencodeRfc3986
     *
     * @param string $input
     * @return multitype: mixed string
     */
    public static function urlencodeRfc3986($input) {
        if (is_array ( $input )) {
            return array_map ( array (
                'SngHttp',
                'urlencodeRfc3986'
            ), $input );
        } else if (is_scalar ( $input )) {
            return str_replace ( '+', ' ', str_replace ( '%7E', '~', rawurlencode ( $input ) ) );
        } else {
            return '';
        }
    }

    /**
     * sng http
     *
     * @param string $url
     * @param string $dataStr
     * @param number $isPost
     * @param string $headers
     * @return mixed
     */
    public function http($url, $dataStr = '', $isPost = 0) {
        $this->httpInfo = array ();
        $ch = curl_init ();

        curl_setopt ( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        curl_setopt ( $ch, CURLOPT_USERAGENT, $this->userAgent );
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $this->timeout );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        $this->header OR $headers = $this->setHeaders ();
        if ($this->header) {
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $this->header );
        }
        if ($isPost) {
            curl_setopt ( $ch, CURLOPT_POST, true );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $dataStr );
            curl_setopt ( $ch, CURLOPT_URL, $url );
        } else {
            curl_setopt ( $ch, CURLOPT_URL, $url . '?' . $dataStr );
        }
        $response = curl_exec ( $ch );
        $this->httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        $this->httpInfo = array_merge ( $this->httpInfo, curl_getinfo ( $ch ) );
        curl_close ( $ch );
        $this->content = $response;
        if ($this->debug) {
            echo "<pre>*******debug start*******<br>";
            echo "=====url======<br>";
            print_r($url . '?' . $dataStr);

            echo "<br>";
            echo '=====post data======' . "<br>";
            var_dump($dataStr);

            echo "<br>";
            echo '=====header info=====' . "<br>";
            print_r($this->header);

            echo "<br>";
            echo '=====http info=====' . "<br>";
            print_r($this->httpInfo);

            echo "<br>";
            echo '=====httpCode info=====' . "<br>";
            print_r($this->httpCode);

            echo "<br>";
            echo '=====response=====' . "<br>";
            print_r($response);

            echo "<br>";
            echo  "*******debug end*******<br>";
        }
        return $response;
    }

    /**
     * set http agent
     *
     * @param string $agent
     */
    public function setUserAgent($agent = '') {
        $this->userAgent = $agent;
    }

    /**
     * set connect timeout
     *
     * @param number $time
     */
    public function setConnectTimeout($time = 5) {
        $this->connectTimeout = $time;
    }

    /**
     * set timeout
     *
     * @param number $time
     */
    public function setTimeout($time = 5) {
        $this->timeout = $time;
    }

    /**
     * get http code
     *
     * @return number
     */
    public function getHttpCode() {
        return $this->httpCode;
    }

    /**
     * get http info
     *
     * @return string
     */
    public function getHttpInfo() {
        return $this->httpInfo;
    }

    /**
     * get http content
     *
     * @return string
     */
    public function getHttpContent() {
        return $this->content;
    }

    /**
     * 生成【联运SDK接口】签名
     *
     * @param array 	$params 表单参数
     * @param string 	$secret 密钥
     */
    public static function makeSign(&$params, $signkey)
    {
        if (isset($params['sign'])) unset($params['sign']);
        if (isset($params['signature'])) unset($params['signature']);
        ksort($params);
        $query_string = array();
        foreach ($params as $key => $val )
        {
            array_push($query_string, $key . '=' . $val);
        }
        $query_string = join('&', $query_string);
        if (!isset($params['appkey']))
        {
            self::outputError(10016, sprintf('missing required parameters: %s', 'appkey'));
        }
        if (empty($signkey))
        {
            self::outputError(10016, sprintf('missing required parameters: %s', 'signkey'));
        }
        $sign_backend = md5(sprintf('%s|%s', $query_string, $signkey));
        return $sign_backend;
    }

    /**
     * 验证【联运SDK接口】签名
     *
     * @param array 	$params 请求参数
     */
    public static function verifySign(&$params, $signkey)
    {
        if (isset($params['sign']))
        {
            empty($sign) && $sign = $params['sign'];
            unset($params['sign']);
        }else if (isset($params['signature']))
        {
            empty($sign) && $sign = $params['signature'];
            unset($params['signature']);
        }
        // 再计算签名
        $sign_backend = self::makeSign($params, $signkey);
        return $sign_backend == $sign;
    }

    /**
     * 生成【支付回调】签名
     * @param array $params	请求参数
     * @param string $appsecret	游戏的app_secret
     */
    public static function makePaySign(&$params, $app_secret)
    {
        if (isset($params['sign'])) unset($params['sign']);
        if (isset($params['signature'])) unset($params['signature']);
        ksort($params);
        $str = '';
        foreach ($params as $key => $value)
        {
            $str .= sprintf('%s|%s|', $key, $value);
        }
        $str .= $app_secret;
        return sha1($str);
    }

    public static function outputError($code, $msg = '', $data = array()) {
        echo (is_array($code) && !empty($code)) ? json_encode($code) : json_encode(self::genErrorMsg($code, $msg, $data));
        exit;
    }

    private static function genErrorMsg($code, $msg, $data = array()) {
        return array(
            'request' => array_key_exists('REDIRECT_URL', $_SERVER) ?
                $_SERVER['REDIRECT_URL'] :
                $_SERVER['REQUEST_URI'],
            'error_code' => $code,
            'error' => $msg
        ) + (is_array($data) && !empty($data) ? $data : array());
    }
}