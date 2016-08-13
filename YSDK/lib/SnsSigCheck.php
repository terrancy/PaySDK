<?php
/**
 * 生成签名类
 *
 */


class SnsSigCheck
{
	/**
	 * 生成签名
	 *
	 * @param string 	$method 请求方法 "get" or "post"
	 * @param string 	$url_path 
	 * @param array 	$params 表单参数
	 * @param string 	$secret 密钥
	 */
    static public function makeSig($method, $url_path, $params, $secret) 
    {
        $mk = self::makeSource($method, $url_path, $params);
        $my_sign = hash_hmac("sha1", $mk, strtr($secret, '-_', '+/'), true);
        $my_sign = base64_encode($my_sign);

        return $my_sign;
    }
    
	static private function makeSource($method, $url_path, $params) 
    {
        $strs = strtoupper($method) . '&' . rawurlencode($url_path) . '&';

        ksort($params);
        $query_string = array();
        foreach ($params as $key => $val ) 
        { 
            array_push($query_string, $key . '=' . $val);
        }   
        $query_string = join('&', $query_string);

        return $strs . str_replace('~', '%7E', rawurlencode($query_string));
    }

	/**
	 * 验证回调发货URL的签名 (注意和普通的OpenAPI签名算法不一样，详见@refer的说明)
     *
	 * @param string 	$method 请求方法 "get" or "post"
	 * @param string 	$url_path 
	 * @param array 	$params 腾讯调用发货回调URL携带的请求参数
	 * @param string 	$secret 密钥
     * @param string 	$sig 腾讯调用发货回调URL时传递的签名
	 *
     * @refer 
     *
	 */
    static public function verifySig($method, $url_path, $params, $secret, $sig) 
    {
        unset($params['sig']);

        // 先使用专用的编码规则对value编码
        foreach ($params as $k => $v)
        {
            $params[$k] = self::encodeValue($v);
        }

        // 再计算签名
        $sig_new = self::makeSig($method, $url_path, $params, $secret);

        return $sig_new == $sig;
    }
    
	/**
	 * 回调发货URL专用的编码算法
	 *  编码规则为：除了 0~9 a~z A~Z !*()之外其他字符按其ASCII码的十六进制加%进行表示，例如"-"编码为"%2D"
     * @refer 
	 */
    static private function encodeValue($value) 
    {
        $rst = '';

        $len = strlen($value);

        for ($i=0; $i<$len; $i++)
        {
            $c = $value[$i];
            if (preg_match ("/[a-zA-Z0-9!\(\)*]{1,1}/", $c))
            {
                $rst .= $c;
            }
            else
            {
                $rst .= ("%" . sprintf("%02X", ord($c)));                                                                                                                
            }   
        }   

        return $rst;
    } 
}


// end of script
