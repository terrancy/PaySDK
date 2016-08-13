<?php
date_default_timezone_set('PRC');

require_once dirname(__FILE__).'/ConfigHelper.php';
require_once dirname(dirname(__FILE__)).'/model/SDKException.php';

/**
 * 使用系统的日志输出功能输出日志，仅在开启debug=true时启用
 */
class LoggerHelper{

    public static function info($msg){
        try{
            if(ConfigHelper::getStrValWithDefault("sdkserver.debug", "false") == "true"){
                error_log(date('y-m-d H:i:s ',time()).$msg."\n", 3, ConfigHelper::getStrValWithDefault("sdkserver.debug.filepath","/var/tmp/")."sdkserver-".date('y-m-d-H',time()).".log");
            }
        }
        //捕获异常
        catch(Exception $e){
            throw new SDKException('写入日志出错，'.$e->getMessage());
        }

    }

}