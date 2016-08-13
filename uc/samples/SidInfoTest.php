<?php

require_once dirname(dirname(__FILE__)).'/service/SDKServerService.php';
require_once dirname(dirname(__FILE__)).'/model/SDKException.php';

//玩家的sid
$sid = "sst1game940c6f01734e47999c48c43f3f60d396123722";
try{
    $sidInfo = SDKServerService::verifySid($sid);
    echo $sidInfo->ucid;
    echo $sidInfo->nickName;
}
catch (SDKException $e){
    echo $e->getCode()." ".$e->getMessage();
}

