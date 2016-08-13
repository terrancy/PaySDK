<?php
require_once dirname(dirname(__FILE__)).'/model/SDKException.php';;
/**
 * 配置文件辅助类
 */
class ConfigHelper{

    private static $config = array();

    public static function getIntVal($key){
        if(is_array(self::$config) && self::$config == null){
            self::$config = include dirname(dirname(__FILE__)).'/config/config.inc.php';
        }

        if(is_array(self::$config) && self::$config != null){
            if(array_key_exists($key, self::$config)){
                return intval(self::$config[$key]);
            }
        }
        else{
            throw new SDKException('配置文件解析错误，请检查/config/config.inc.php');
        }
    }

    public static function getStrVal($key){
        if(is_array(self::$config) && self::$config == null){
            self::$config = include dirname(dirname(__FILE__)).'/config/config.inc.php';
        }

        if(is_array(self::$config) && self::$config != null){
            if(array_key_exists($key, self::$config)){
                return self::$config[$key];
            }
        }
        else{
            throw new SDKException('配置文件解析错误，请检查/config/config.inc.php');
        }
    }

    public static function getIntValWithDefault($key, $default){
        if(is_array(self::$config) && self::$config == null){
            self::$config = include dirname(dirname(__FILE__)).'/config/config.inc.php';
        }

        if(is_array(self::$config) && self::$config != null){
            if(array_key_exists($key, self::$config)){
                return intval(self::$config[$key]);
            }
            return $default;
        }
        else{
            throw new SDKException('配置文件解析错误，请检查/config/config.inc.php');
        }
    }

    public static function getStrValWithDefault($key, $default){
        if(is_array(self::$config) && self::$config == null){
            self::$config = include dirname(dirname(__FILE__)).'/config/config.inc.php';
        }

        if(is_array(self::$config) && self::$config != null){
            if(array_key_exists($key, self::$config)){
                return self::$config[$key];
            }
            else{
                return $default;
            }
        }
        else{
            throw new SDKException('配置文件解析错误，请检查/config/config.inc.php');
        }
    }

}