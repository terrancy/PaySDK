<?php

require_once dirname(dirname(__FILE__)).'/util/LoggerHelper.php';
require_once dirname(dirname(__FILE__)).'/util/Block.php';
require_once dirname(dirname(__FILE__)).'/model/DomainInfo.php';
require_once dirname(dirname(__FILE__)).'/constant/ServiceName.php';

/**
 * 天雷系统服务类
 */
class DomainServerService{

    private $domain_server = array();

    private $expireTime = 300000;

    function get_cache_id($domainServerReqBody) {
        // maintain list of caches here
        $id=array(
            ServiceName::$DOMAINSERVER_REQ_BODY => 6559,
            ServiceName::$DOMAINSERVER_GAMEDATA_BODY => 6560
        );
        return $id[$domainServerReqBody];
    }

    public function getDomainByServer($domainServerReqBody, $ip = null){
        $domainInfo = null;

        if(extension_loaded('shmop') && empty($ip)){
            $memory = new Block(self::get_cache_id($domainServerReqBody));
            $domainStr = $memory->read();
            if(empty($domainStr)){
                $domainInfo =  self::getDomainInfoByMutiThread($domainServerReqBody);
                //新数据，存储到共享区
                $memory->write(json_encode($domainInfo));
                LoggerHelper::info("缓存中不存在智能域名解析系统的IP缓存，直接请求智能域名解析系统。");
                return $domainInfo;
            }
            else{
                $domianArray = json_decode($domainStr);
                $ctime = $domianArray->{'ctime'};
                $currentTime = self::getMillisecond();
                if($currentTime - $ctime > $this->expireTime){
                    //已过期，需要重新获取
                    $domainInfo =  self::getDomainInfoByMutiThread($domainServerReqBody);
                    $memory->write(json_encode($domainInfo));
                    LoggerHelper::info("缓存中的智能域名解析系统的IP缓存已过期，再次请求智能域名解析系统。");
                    return $domainInfo;
                }
                else{
                    LoggerHelper::info("缓存中的智能域名解析系统的IP缓存有效，从缓存直接获取的信息为：".$domainStr);
                    $domainInfo = new DomainInfo();
                    $domainInfo->ipAddress = $domianArray->{'ipAddress'};
                    $domainInfo->domain = $domianArray->{'domain'};
                    $domainInfo->ctime = $domianArray->{'ctime'};
                    return $domainInfo;
                }
            }
        }
        else{
            if(!empty($ip)){
                LoggerHelper::info("上一次的ip=".$ip."无法返回有效的响应,重新请求智能域名解析系统获取新ip。");
            }
            else{
                LoggerHelper::info("未开启内存读取shmop模块,直接请求智能域名解析系统。");
            }
            $domainInfo =  self::getDomainInfoByMutiThread($domainServerReqBody, $ip);
            return $domainInfo;
        }


    }

    public function getDomainByCache($domainServerReqBody){
        $domainInfo = null;

        if(extension_loaded('shmop')){
            $memory = new Block(self::get_cache_id($domainServerReqBody));
            $domainStr = $memory->read();
            if(empty($domainStr)){
                LoggerHelper::info("缓存中不存在智能域名解析系统的IP缓存，直接请求域名.");
                return null;
            }
            else{
                $domianArray = json_decode($domainStr);
                $ctime = $domianArray->{'ctime'};
                $currentTime = self::getMillisecond();
                if($currentTime - $ctime > $this->expireTime){
                    //已过期,删除
                    $memory->delete();
                    LoggerHelper::info("缓存中的智能域名解析系统的IP缓存已过期，直接请求域名。");
                    return null;
                }
                else{
                    LoggerHelper::info("缓存中的智能域名解析系统的IP缓存有效，从缓存直接获取的信息为：".$domainStr);
                    $domainInfo = new DomainInfo();
                    $domainInfo->ipAddress = $domianArray->{'ipAddress'};
                    $domainInfo->domain = $domianArray->{'domain'};
                    $domainInfo->ctime = $domianArray->{'ctime'};
                    return $domainInfo;
                }
            }
        }
        else{
            LoggerHelper::info("未开启内存读取shmop模块,无缓存设置，返回。");
            return null;
        }
    }

    private function getDomainInfoByMutiThread($domainServerReqBody, $ip = null){
        //初始化智能域名解析系统的地址
        if(is_array($this->domain_server) && $this->domain_server == null){
             array_push($this->domain_server, "http://119.147.224.168:8080",
                 "http://183.233.224.202:8080", "http://163.177.128.251:8080");
        }

        $mh = curl_multi_init();
        $reqBody = empty($ip) ? $domainServerReqBody : $domainServerReqBody."_".$ip;
        $conn = array();
        foreach ($this->domain_server as $i => $url) {
            $conn[$i] = curl_init($url.ServiceName::$DOMAINSERVER_SERVICE);
            curl_setopt($conn[$i], CURLOPT_USERAGENT, "UCSDK");
            curl_setopt($conn[$i], CURLOPT_POST, true);
            curl_setopt($conn[$i], CURLOPT_HEADER, false);
            curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($conn[$i], CURLOPT_POSTFIELDS, $reqBody);
            curl_setopt($conn[$i], CURLOPT_CONNECTTIMEOUT,ConfigHelper::getIntValWithDefault("connectTimeOut", 60));
            curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER,true);  // 设置不将爬取代码写到浏览器，而是转化为字符串
            curl_multi_add_handle ($mh,$conn[$i]);
        }

        do {
            curl_multi_exec($mh,$active);
        } while ($active);

        $resp_data = null;
        foreach ($this->domain_server as $i => $url) {
            $resp_data = curl_multi_getcontent($conn[$i]); // 获得返回值
            if($resp_data != null){
                break; //终止循环
            }
        }

        foreach ($this->domain_server as $i => $url) {
            curl_multi_remove_handle($mh,$conn[$i]);
            curl_close($conn[$i]);
        }

        curl_multi_close($mh);

        $mutiServerAddress = explode("|", $resp_data);
        if(is_array($mutiServerAddress) && $mutiServerAddress == null){
            LoggerHelper::info("获取到智能域名解析服务端接口返回的IP地址为空。");
            return null;
        }
        $serverInfo = explode("_", $mutiServerAddress[0]);
        if(is_array($serverInfo) && $serverInfo == null){
            LoggerHelper::info("获取到智能域名解析服务端接口返回的IP地址为空。");
            return null;
        }

        $domainInfo = new DomainInfo();
        $domainInfo->domain = $serverInfo[0];
        $domainInfo->ipAddress = $serverInfo[1];
        $domainInfo->ctime= self::getMillisecond();
        LoggerHelper::info("获取到智能域名解析服务端接口返回的IP地址domain=".$domainInfo->domain.",ipAddress=".$domainInfo->ipAddress."。");

        return $domainInfo;
    }

    /**
     * 获取毫秒级的时间参数
     *
     */
     private function getMillisecond() {
        $time = explode ( " ", microtime () );
        $time = $time [1] . ($time [0] * 1000);
        $time2 = explode ( ".", $time );
        $time = $time2 [0];
        return $time;
    }
}