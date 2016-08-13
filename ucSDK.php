<?php

class ucSDK{

    private $resultSuccess = 0;
    private $resultFailed = 1;

    function __construct($idApp="",$keyApi=""){
        if(!empty($idApp)){
            $this->_idApp = $idApp;
        }
        if(!empty($keyApi)){
            $this->_keyApi = $keyApi;
        }

    }


    function getUCBaseInit(){
        require_once dirname(__FILE__).'/uc/service/SDKServerService.php';
        require_once dirname(__FILE__).'/uc/model/SDKException.php';
    }


    function getUCPayInit(){
        require_once dirname(__FILE__).'/uc/util/ConfigHelper.php';
        require_once dirname(__FILE__).'/uc/util/LoggerHelper.php';
    }


    //登录
    function getUCUserLogin($sid){
        if(!empty($sid)){
//            $sid = "cst1mobi033d6f43069846be949bb161a473cf30147562";
            $this->getUCBaseInit();
            try{
                $sidInfo = SDKServerService::verifySession($sid);
//                echo $sidInfo->ucid;
//                echo $sidInfo->nickName;
                $arrRst['result'] = $this->resultSuccess;
                $arrRst['data'] = $sidInfo;
            } catch (SDKException $e){
//                echo $e->getCode()." ".$e->getMessage();
                $arrRst['result'] = $this->resultFailed;
                $arrRst['infoError'] = array(
                    "code"      =>      $e->getCode(),
                    "msg"       =>       $e->getMessage(),
                );
            }
        }
        return empty($arrRst) ? array() : $arrRst;
    }

    //上传游戏数据
    function getUCGameDataUpload($sid,$gameContent){
        if(!empty($sid)){
            $this->getUCBaseInit();
            try{
                $gameData = !empty($gameContent)&& is_array($gameContent) ? array($gameContent) : array();
                $result = SDKServerService::gameData($sid, $gameData);
//            if($result){echo "上传成功";};
                $arrRst['result'] = $this->resultSuccess;
                $arrRst['data'] = $result;
            } catch (SDKException $e){
                // echo $e->getCode()." ".$e->getMessage();
                $arrRst['result'] = $this->resultFailed;
                $arrRst['infoError'] = array(
                    "code"      =>      $e->getCode(),
                    "msg"       =>       $e->getMessage(),
                );
            }
        }
        return empty($arrRst) ? array() : $arrRst;
    }

    //玩家充值
    function getUCPayCallBackService($dataPay){
//        $arrRst = array(
//            "data"      =>      $dataPay,
//            "result"        =>      $this->resultSuccess,
//        );
//        return $arrRst;
        try{
            // 接收HTTP POST信息
            //$request = file_get_contents("php://input");

            // 测试数据
//            $dataPay = '{"sign":"1ebc3d652404c8ac9b834a2aba9bb98a","data":{"failedDesc":"","amount":"30.0","callbackInfo":"serverip=sgall#channel=502#user=288287224.uc","accountId":"10000","creator":"JY","gameId":"1","payWay":"1","serverId":"1132","orderStatus":"S","orderId":"20120312113248863160","cpOrderId":"987654321"}}';
            $this->getUCBaseInit();
            $this->getUCPayInit();
            LoggerHelper::info("[PayCallbackService.php]收到的支付回调的请求：".$dataPay);

            // 处理支付回调请求
            $responseData = json_decode($dataPay,true);
            $arrRst['result'] = $this->resultFailed;
            $arrRst['info'] = "FAILURE";
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

                $baseService = new BaseSDKService();
                $signSource = $baseService->getSignData($responseData['data']).ConfigHelper::getStrVal("sdkserver.game.apikey");//组装签名原文
                $sign = md5($signSource);//MD5加密签名

                LoggerHelper::info("[PayCallbackService.php]"."[签名原文]:".$signSource);
                LoggerHelper::info("[PayCallbackService.php]"."[签名结果]:".$sign);
                if($sign == $responseData['sign']){
                    if ("S"==$responseData['data']['orderStatus']) {
                        LoggerHelper::info("[PayCallbackService.php]"."[处理结果]:"."SUCCESS");
                        $arrRst['result'] = $this->resultSuccess;
                        $arrRst['info'] = "SUCCESS";
                    }
                }else{
                    LoggerHelper::info("[PayCallbackService.php]"."[处理结果]:"."FAILURE");
                }
            }else{
                LoggerHelper::info("[PayCallbackService.php]"."接口返回异常");
            }
            return $arrRst;
        }
        catch (SDKException $e){
            LoggerHelper::info("[PayCallbackService.php]".$e->getMessage());
//            throw new exception($e->getMessage());
        }
    }

}