<?php
/**
 * 调用Sdk查询订单
 * 
 */
require_once 'Sdk.php';

$sdk = new Sdk();

//客户端SDK返回的登陆令牌
$accessToken = "";//

$Res = $sdk->login_state_result($accessToken);

print_r($Res);

if($Res['ResultCode']=="1"&&$Res['Sign']==$sdk->SignMd5($Res['ResultCode'],urldecode($Res['Content']))){
	//Content参数需要urldecode后再进行base64解码
	$result=base64_decode(urldecode($Res['Content']));
	print($result);
	//json解析
	$Item=extract(json_decode($result,true));
	//根据获取的信息，执行业务处理


	//打印$Item信息
	print($UID);
}
?>