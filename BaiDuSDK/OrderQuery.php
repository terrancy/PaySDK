<?php
/**
 * 调用Sdk查询订单
 * 
 */
require_once 'Sdk.php';

$sdk = new Sdk();

//商户订单号
$CooperatorOrderSerial = "";//eg:40DDF8AC-6BE3-4F72-A10B-A057ADE3093C

$Res = $sdk->query_order_result($CooperatorOrderSerial);

print_r($Res);

if($Res['ResultCode']=="1"&&$Res['Sign']==$sdk->SignMd5($Res['ResultCode'],urldecode($Res['Content']))){
	//Content参数需要urldecode后再进行base64解码
	$result=base64_decode(urldecode($Res['Content']));
	print($result);
	//json解析
	$Item=extract(json_decode($result,true));
	//根据获取的信息，执行业务处理


	//打印$Item信息
	print($OrderSerial);
	print($MerchandiseName);
}
?>