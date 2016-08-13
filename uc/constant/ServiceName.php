<?php

/**
 *@desc    class
 *@date    2015-5-8
**/
class ServiceName {

    /**
     * 老接口全部使用ss的内部域
     */
    public static $SS_PREFIX = "ss/";

    /**
     * 新接口使用cp的内部域
     */
    public static $CP_PREFIX = "cp/";

    /**
     * 游戏数据提交的新接口前缀
     */
    public static $CP_GAMEDATA_PREFIX = "ng/cpserver/gamedata/";


    /**
     * 天雷系统的服务名
     */
    public static $DOMAINSERVER_SERVICE = "/httpdns/request";

    /**
     * 服务端的端口配置
     */
    public static $SDK_SERVER_PORT = "80";
    public static $SDK_GAMEDATA_PORT = "8080";


    /**
     * 天雷系统的请求报文(参数指定)
     */
    public static $DOMAINSERVER_REQ_BODY = "1|sdk.g.uc.cn";

    /**
     * 智能域名系统的请求报文(参数指定)
     */
    public static $DOMAINSERVER_GAMEDATA_BODY = "1|collect.sdknc.g.uc.cn";


    /**
     * 智能域名系统的本地属性KEY
     */
    public static $DOMAINSERVER_ATTR_KEY = "domain_server_attr_key";

    /**
     * UCID绑定接口
     */
    public static $SIDINFO_SERVICE = "ucid.user.sidInfo";
    public static $VERIFYSESSION_SERVICE = "account.verifySession";
    public static $GAMEDATA_SERVICE = "ucid.game.gameData";

}