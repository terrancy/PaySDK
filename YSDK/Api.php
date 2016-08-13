<?php
/**
 * PHP SDK for YSDK V1.0.0
 */


require_once __DIR__ .'/lib/SnsNetwork.php';
require_once __DIR__ .'/lib/SnsSigCheck.php';


/**
 * 如果您的 PHP 没有安装 cURL 扩展，请先安装
 */
if (!function_exists('curl_init'))
{
    throw new Exception('OpenAPI needs the cURL PHP extension.');
}

/**
 * 如果您的 PHP 不支持JSON，请升级到 PHP 5.2.x 以上版本
 */
if (!function_exists('json_decode'))
{
    throw new Exception('OpenAPI needs the JSON PHP extension.');
}

/**
 * 错误码定义
 */
define('OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY', 1801); // 参数为空
define('OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID', 1802); // 参数格式错误
define('OPENAPI_ERROR_RESPONSE_DATA_INVALID', 1803); // 返回包格式错误
define('OPENAPI_ERROR_CURL', 1900); // 网络错误, 偏移量1900, 详见 http://curl.haxx.se/libcurl/c/libcurl-errors.html

/**
 * 提供访问腾讯开放平台 YSDK 的接口
 */
class Api
{
    private $appid  = 0;
    private $appkey = '';
    private $pay_appid = 0;
    private $pay_appkey = '';
    private $server_name = '';
    private $format = 'json';
    private $sdk_version = 'PHP YSDK v1.0.0';

    /**
     * 构造函数
     *
     * @param int $appid 应用的ID
     * @param string $appkey 应用的密钥
     */
    function __construct($appid, $appkey)
    {
        $this->appid = $appid;
        $this->appkey = $appkey;
    }

    public function setPay($pay_appid, $pay_appkey)
    {
        $this->pay_appid = $pay_appid;
        $this->pay_appkey = $pay_appkey;
    }

    public function setServerName($server_name)
    {
        $this->server_name = $server_name;
    }

    public function getAppid()
    {
        return $this->appid;
    }

    public function getAppkey()
    {
        return $this->appkey;
    }

    /**
     * 执行API调用，返回结果数组
     *
     * @param string $script_name 调用的API方法，比如/auth/verify_login，
     *                             参考 http://wiki.dev.4g.qq.com/v2/ZH_CN/router/index.html#!qq.md#2.1 Oauth服务
     * @param array  $params 调用API时带的参数
     * @param string $method 请求方法 post
     * @param string $protocol 协议类型 http / https
     * @return array 结果数组
     */
    public function api_ysdk($script_name, $params,  $method='post', $protocol='http')
    {

        // add some params: 'version'
        $params['version'] = $this->sdk_version;  
        
        $url = $protocol . '://' . $this->server_name . $script_name;

        // 通过调用以下方法，可以打印出最终发送到YSDK API服务器的请求参数以及url，默认为注释
        self::printRequest($url,$params,$method);

        $cookie = array();

        // 发起请求
        $ret = SnsNetwork::makeRequest($url, $params, $cookie, $method, $protocol);

        if (false === $ret['result'])
        {
            $result_array = array(
                'ret' => OPENAPI_ERROR_CURL + $ret['errno'],
                'msg' => $ret['msg'],
            );
        }
        else
        {
	        $result_array = json_decode($ret['msg'], true);

	        // 远程返回的不是 json 格式, 说明返回包有问题
	        if (is_null($result_array)) {
	            $result_array = array(
	                'ret' => OPENAPI_ERROR_RESPONSE_DATA_INVALID,
	                'msg' => $ret['msg']
	            );
	        }
		}

        // 通过调用以下方法，可以打印出调用openapi请求的返回码以及错误信息，默认注释
        self::printRespond($result_array);

        return $result_array;
    }


    public function api_pay($script_name,$accout_type,$params,$method='post', $protocol='http')
    {

        // 添加一些参数
        $params['appid'] = $this->pay_appid;
        $params['format'] = $this->format;

		$cookie=array();
		$cookie["org_loc"] = urlencode($script_name);
		if( $accout_type == "qq")
		{
			$cookie["session_id"] = "openid";
			$cookie["session_type"] = "kp_actoken";
		}
		else if( $accout_type == "wx" )
		{
			$cookie["session_id"] = "hy_gameid";
			$cookie["session_type"] = "wc_actoken";
		}
		else
		{
			return OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID;
		}

        

        // 无需传sig, 会自动生成
        unset($params['sig']);

        // 生成签名
        $secret = $this->pay_appkey.'&';

        $script_sig_name="/v3/r".$script_name;
        $sig = SnsSigCheck::makeSig($method, $script_sig_name, $params, $secret);
        $params['sig'] = $sig;

        $url = $protocol . '://' . $this->server_name . $script_name;

        // 通过调用以下方法，可以打印出最终发送到openapi服务器的请求参数以及url，默认为注释
        self::printCookies($cookie);
        self::printRequest($url,$params,$method);


        // 发起请求
        $ret = SnsNetwork::makeRequest($url, $params, $cookie, $method, $protocol);
//        echo json_encode($params);
        if (false === $ret['result'])
        {
            $result_array = array(
                'ret' => OPENAPI_ERROR_CURL + $ret['errno'],
                'msg' => $ret['msg'],
            );
        }

        $result_array = json_decode($ret['msg'], true);

        // 远程返回的不是 json 格式, 说明返回包有问题
        if (is_null($result_array)) {
            $result_array = array(
                'ret' => OPENAPI_ERROR_RESPONSE_DATA_INVALID,
                'msg' => $ret['msg']
            );
        }

        // 通过调用以下方法，可以打印出调用支付API请求的返回码以及错误信息，默认注释
        self::printRespond($result_array);

        return $result_array;
    }


    /**
     * 打印出请求串的内容，当API中的这个函数的注释放开将会被调用。
     *
     * @param string $url 请求串内容
     * @param array $params 请求串的参数，必须是array
     * @param string $method 请求的方法 get / post
     */
    private function printRequest($url, $params, $method)
    {
        $query_string = SnsNetwork::makeQueryString($params);
        if($method == 'get')
        {
            $url = $url."?".$query_string;
        }
        // echo '<pre>';
        // echo "\n============= request info ================\n\n";
        //print_r("method : ".$method."\n");
        //print_r("url    : ".$url."\n");

        if($method == 'post')
        {
            //print_r("query_string : ".$query_string."\n");
        }
        // echo "\n";
        //print_r("params : ".//print_r($params, true)."\n");
        // echo "\n";

    }

    /**
     * 打印出请求的cookies，当API中的这个函数的注释放开将会被调用。
     *
     * @param array $cookies 待打印的cookies
     */
    private function printCookies($cookies)
    {
        // echo "\n============= cookie info ================\n\n";
        //print_r("cookies : ".//print_r($cookies, true)."\n");
        // echo "\n";

    }

    /**
     * 打印出返回结果的内容，当API中的这个函数的注释放开将会被调用。
     *
     * @param array $array 待打印的array
     */
    private function printRespond($array)
    {
        // echo "\n============= respond info ================\n\n";
        //print_r($array);
        // echo "\n";
    }

}

// end of script
