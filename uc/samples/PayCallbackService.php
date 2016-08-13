<?php

require_once dirname(dirname(__FILE__)).'/service/SDKServerService.php';
require_once dirname(dirname(__FILE__)).'/model/SDKException.php';
require_once dirname(dirname(__FILE__)).'/util/ConfigHelper.php';
require_once dirname(dirname(__FILE__)).'/util/LoggerHelper.php';

try{
    // 接收HTTP POST信息
    //$request = file_get_contents("php://input");

    // 测试数据
    $request = '{"sign":"1ebc3d652404c8ac9b834a2aba9bb98a","data":{"failedDesc":"","amount":"30.0","callbackInfo":"serverip=sgall#channel=502#user=288287224.uc","accountId":"10000","creator":"JY","gameId":"1","payWay":"1","serverId":"1132","orderStatus":"S","orderId":"20120312113248863160","cpOrderId":"987654321"}}';
    LoggerHelper::info("[PayCallbackService.php]收到的支付回调的请求：".$request);

    // 处理支付回调请求
    $responseData = json_decode($request,true);
    if($responseData!=null){
        LoggerHelper::info("[PayCallbackService.php]"."[sign]:".$responseData['sign']);
        LoggerHelper::info("[PayCallbackService.php]"."[orderId]:".$responseData['data']['orderId']);
        LoggerHelper::info("[PayCallbackService.php]"."[gameId]:".$responseData['data']['gameId']);
        LoggerHelper::info("[PayCallbackService.php]"."[accountId]:".$responseData['data']['accountId']);
        LoggerHelper::info("[PayCallbackService.php]"."[creator]:".$responseData['data']['creator']);
        LoggerHelper::info("[PayCallbackService.php]"."[payWay]:".$responseData['data']['payWay']);
        LoggerHelper::info("[PayCallbackService.php]"."[amount]:".$responseData['data']['amount']);
        LoggerHelper::info("[PayCallbackService.php]"."[callbackInfo]:".$responseData['data']['callbackInfo']);
        LoggerHelper::info("[PayCallbackService.php]"."[orderStatus]:".$responseData['data']['orderStatus']);
        LoggerHelper::info("[PayCallbackService.php]"."[failedDesc]:".$responseData['data']['failedDesc']);
        LoggerHelper::info("[PayCallbackService.php]"."[cpOrderId]:".$responseData['data']['cpOrderId']);

        $baseService = new BaseSDKService();
        $signSource = $baseService->getSignData($responseData['data']).ConfigHelper::getStrVal("sdkserver.game.apikey");//组装签名原文
        $sign = md5($signSource);//MD5加密签名

        LoggerHelper::info("[PayCallbackService.php]"."[签名原文]:".$signSource);
        LoggerHelper::info("[PayCallbackService.php]"."[签名结果]:".$sign);
        if($sign == $responseData['sign']){
            if ("S"==$responseData['data']['orderStatus']) {
                if ("30.0"==$responseData['data']['amount']||"88"==$responseData['data']['amount']) {
                    /*
                    * 游戏服务器需要处理给玩家充值代码,由游戏合作商开发完成。
                    */
                    /*
                    * 游戏需根据orderStatus参数的值判断是否给玩家过账虚拟货币。（S为充值成功、F为充值失败，避免假卡、无效卡充值成功）
                    *定额充值的游戏需要校验amount值避免玩家窜改了客户端下单时的金额，影响游戏收支平衡
                    */
                    LoggerHelper::info("[PayCallbackService.php]"."[处理结果]:"."SUCCESS");
                    return 'SUCCESS';//返回给sdk server的响应内容
                }
            }
        }
        LoggerHelper::info("[PayCallbackService.php]"."[处理结果]:"."FAILURE");
        return 'FAILURE';//返回给sdk server的响应内容 ,对于重复多次通知失败的订单,请参考文档中通知机制。
    }
else{
        LoggerHelper::info("[PayCallbackService.php]"."接口返回异常");
        throw new exception('接口返回异常');
    }

}
catch (SDKException $e){
    LoggerHelper::info("[PayCallbackService.php]".$e->getMessage());
    throw new exception($e->getMessage());
}