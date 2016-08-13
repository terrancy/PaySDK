<?php
require_once dirname(dirname(__FILE__)).'/util/HttpClient.php';
require_once dirname(dirname(__FILE__)).'/util/ConfigHelper.php';
require_once dirname(dirname(__FILE__)).'/util/LoggerHelper.php';

require_once dirname(dirname(__FILE__)).'/model/DomainInfo.php';
require_once dirname(dirname(__FILE__)).'/constant/StateCode.php';
require_once dirname(dirname(__FILE__)).'/constant/ServiceName.php';
require_once dirname(dirname(__FILE__)).'/constant/SDKVersion.php';
require_once dirname(dirname(__FILE__)).'/service/DomainServerService.php';

/**
 *  请求服务端数据组装类
 */
class BaseSDKService{

    protected static function getSDKServerResponse (array $params, $prefix, $serviceName){
        return BaseSDKService::getServerResponse(ConfigHelper::getStrVal("sdkserver.baseUrl"),
            ConfigHelper::getStrValWithDefault("sdkserver.baseUrl.port", ServiceName::$SDK_SERVER_PORT),
            ServiceName::$DOMAINSERVER_REQ_BODY, $params, $prefix, $serviceName);
    }

    protected static function getSDKGameDataResponse (array $params, $prefix, $serviceName){
        return BaseSDKService::getServerResponse(ConfigHelper::getStrVal("sdkgamedata.baseUrl"),
            ConfigHelper::getStrValWithDefault("sdkgamedata.baseUrl.port", ServiceName::$SDK_GAMEDATA_PORT),
            ServiceName::$DOMAINSERVER_GAMEDATA_BODY, $params, $prefix, $serviceName);
    }


    /**
     * 获取SDKServer服务端的响应
     * @param array $params
     * @param $prefix
     * @param $serviceName
     * @return StdClass
     * @throws SDKException
     */
    private static function getServerResponse($serverBaseUrl, $serverPort, $domainServerReqBody, array $params, $prefix, $serviceName){
        try {
            $domainService = new DomainServerService();
            $requestParam = BaseSDKService::assemblyParameters($serviceName,$params);
            //print_r($requestParam);
            $requestBody = json_encode($requestParam);//把参数序列化成一个json字符串
            $responseBody = false;
            $domainCache = $domainService->getDomainByCache($domainServerReqBody);

            try {
                if(!empty($domainCache)){
                    LoggerHelper::info("智能域名解析系统缓存存在，直接使用智能域名解析系统返回的IP".BaseSDKService::assemblyUrl("http://".$domainCache->ipAddress, $serverPort, $prefix , $serviceName)."访问服务端.");
                    $responseBody = HttpClient::quickPost(BaseSDKService::assemblyUrl("http://".$domainCache->ipAddress, $serverPort, $prefix , $serviceName), $requestBody);
                }
                else{
                    $responseBody = HttpClient::quickPost(BaseSDKService::assemblyUrl($serverBaseUrl, $serverPort, $prefix , $serviceName), $requestBody);
                }
            }catch (Exception $e) {
                $responseBody = false;
            }

            if(!$responseBody){//请求异常时返回false
                $ip = empty($domainCache) ? null : $domainCache->ipAddress;//如果是缓存中的值，且缓存获取返回值失败，排除缓存中的值
                //使用智能域名解析客户端
                $domainInfo = $domainService->getDomainByServer($domainServerReqBody, $ip);
                if($domainInfo == null){
                    LoggerHelper::info("请求常规接口".BaseSDKService::assemblyUrl($serverBaseUrl, $serverPort, $prefix , $serviceName)."失败，请求智能域名解析也失败.");
                    throw new SDKException("请求接口失败");
                }

                //重新使用智能域名解析客户端返回的ip地址进行请求
                $responseBody = HttpClient::quickPost(BaseSDKService::assemblyUrl("http://".$domainInfo->ipAddress, $serverPort, $prefix , $serviceName), $requestBody);
            }

            $responseObj = null;
            try {
                $responseObj = json_decode($responseBody, true);
            } catch (Exception $e) {
                $ip = empty($domainInfo) ? (empty($domainCache) ? null : $domainCache->ipAddress) : $domainInfo->ipAddress;//如果是缓存中的值，且缓存获取返回值失败，排除缓存中的值
                // 无法正确解析报文，使用智能域名解析客户端
                $domainInfo = $domainService->getDomainByServer($ip);
                if($domainInfo == null){
                    LoggerHelper::info("请求常规接口".BaseSDKService::assemblyUrl($serverBaseUrl, $serverPort, $prefix , $serviceName)."失败，请求智能域名解析也失败.");
                    throw new SDKException("请求接口失败");
                }

                //重新使用智能域名解析客户端返回的ip地址进行请求
                $responseBody = HttpClient::quickPost(BaseSDKService::assemblyUrl("http://".$domainInfo->ipAddress, $serverPort, $prefix , $serviceName), $requestBody);
                try {
                    $responseObj = json_decode($responseBody, true);
                } catch (Exception $jsondecodeerror) {
                    LoggerHelper::info("请求常规接口".BaseSDKService::assemblyUrl("http://".$domainInfo->ipAddress, $serverPort, $prefix , $serviceName)."，无法解析响应.".$jsondecodeerror->getMessage());
                    throw new SDKException($jsondecodeerror->getMessage(), -1);
                }
            }

            if($responseObj == null){
                throw new SDKException("请求接口无响应", -1);
            }

            if($responseObj["state"]["code"] != StateCode::$SUCCESS){
                //接口返回失败，以异常的形式抛出
                LoggerHelper::info("请求接口状态码不为成功.".$responseBody);
                throw new SDKException($responseObj["state"]["msg"], $responseObj["state"]["code"]);
            }

            //返回的结果集安全解析
            return $responseObj["data"];
        } catch (Exception $e) {
            if($e instanceof SDKException){
                throw $e;
            }
            LoggerHelper::info("请求接口调用出错.".$e->getMessage());
            throw new SDKException($e->getMessage(), -1);
        }
    }

    /**
     * 获取毫秒级的时间参数
     *
     */
     private static  function getMillisecond() {
        $time = explode ( " ", microtime () );
        $time = $time [1] . ($time [0] * 1000);
        $time2 = explode ( ".", $time );
        $time = $time2 [0];
        return $time;
    }

    /**
     * 按字母排序$params数组d的键,返回键.值的加密内容
     * @param $data 业务数据
     */
     public static function getSignData(array $params){
        ksort($params);
        $enData = '';
        foreach( $params as $key=>$val ){
            $enData = $enData.$key.'='.$val;
        }
        return $enData;
    }

    /**
     * 封装访问入参数据
     * @param $serviceName
     * @param array $params
     * @return array
     */
     private static function assemblyParameters($serviceName,array $params){
        /////////////////组装游戏参数-开始/////////////////////
        $gameParam = array();
        $gameParam["cpId"] = ConfigHelper::getIntValWithDefault("sdkserver.game.cpId", 0);//gameid是在游戏接入时由UC平台分配
        $gameParam["gameId"] = ConfigHelper::getIntVal("sdkserver.game.gameId");
        $gameParam["channelId"] = ConfigHelper::getStrValWithDefault("sdkserver.game.channelId", "");
        $gameParam["serverId"] = ConfigHelper::getIntValWithDefault("sdkserver.game.serverId", 0);
        $gameParam["serverName"] = ConfigHelper::getStrValWithDefault("sdkserver.game.serverName", "");
        //////////////////组装游戏参数-结束////////////////////
        /*
        签名规则=签名内容.apiKey
        假定apiKey=202cb962234w4ers2aaa,sid=abcdefg123456
        那么签名原文sid=abcdefg123456202cb962234w4ers2aaa
        签名结果6e9c3c1e7d99293dfc0c81442f9a9984
        */
        $signSource = BaseSDKService::getSignData($params).ConfigHelper::getStrVal("sdkserver.game.apikey");//组装签名原文
        $sign = md5($signSource);//MD5加密签名

        $ex = array();
        $ex["ex"] = "language:".SDKVersion::$LANGUAGE ."|version:".SDKVersion::$VERSION;
        ///////////////////组装请求参数-开始////////////////////
        $requestParam = array();
        $requestParam["id"] = BaseSDKService::getMillisecond();//当前系统时间（毫秒）
        $requestParam["service"] = $serviceName ;//"account.verifySession";
        $requestParam["game"] = $gameParam;
        $requestParam["client"] = $ex;
        if(count($params) == 0){
            $requestParam["data"] = new stdClass();
        }else{
            $requestParam["data"] = $params;
        }
        $requestParam["encrypt"] = "md5";
        $requestParam["sign"] = $sign;
        ///////////////////组装请求参数-结束/////////////////////

        return $requestParam;

    }

     private static function assemblyUrl($sdkServerBaseUrl, $serverPort, $prefix, $serviceName){
        return $sdkServerBaseUrl.":".$serverPort."/".$prefix.$serviceName;
    }
}