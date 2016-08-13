<?php

/**
 *类名：trade.php
 *功能  服务器端创建交易Demo
 *版本：1.0
 *日期：2014-06-26
 '说明：
 '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己的需要，按照技术文档编写,并非一定要使用该代码。
 '该代码仅供学习和研究爱贝云计费接口使用，只是提供一个参考。
*/
require_once("config.php");
require_once ("base.php");
    
function testOrder() {
    global $orderUrl, $cpvkey, $platpkey;
    //下单接口
    $orderReq["appid"] = "500000185";
    $orderReq["waresid"] = 1;
    $orderReq["cporderid"] = "00000000001"; 
    $orderReq["price"] = 1.00;   //单位：元
    $orderReq["currency"] = "RMB";
    $orderReq["appuserid"] = "test";
    $orderReq["cpprivateinfo"] = "test";
    $orderReq["notifyurl"] = "http://xxxxxxx/xxx";

    //组装请求报文
    $reqData = composeReq($orderReq, $cpvkey);

    //发送到爱贝服务后台
    $respData = request_by_curl($orderUrl, $reqData, "order test");
    echo "respData:$respData\n";
   
    //返回报文解析
    if(!parseResp($respData, $platpkey, $respJson)) {
        echo "parse resp data failed!\n";
    }
    print_r($respJson);
}

function testQueryResult() {
    global $queryResultUrl, $cpvkey, $platpkey;
    //支付结果查询接口
    $queryReq["appid"] = "500000185";
    $queryReq["cporderid"] = "00000000001"; 

    //组装请求报文
    $reqData = composeReq($queryReq, $cpvkey);

    //发送到爱贝服务后台
    $respData = request_by_curl($queryResultUrl, $reqData, "queryResult test");
    echo "respData:$respData\n";
   
    //返回报文解析
    if(!parseResp($respData, $platpkey, $respJson)) {
        echo "parse resp data failed!\n";
    }
    print_r($respJson);
}

//testOrder();
testQueryResult();

?>