<?php

require_once dirname(dirname(__FILE__)).'/service/SDKServerService.php';
require_once dirname(dirname(__FILE__)).'/model/SDKException.php';

//玩家的sid
$sid = "sst1game940c6f01734e47999c48c43f3f60d396123722";
try{
    $sessionInfo = SDKServerService::verifySession($sid);
    echo $sessionInfo->accountId;
    echo $sessionInfo->nickName;
    echo $sessionInfo->creator;
}
catch (SDKException $e){
    echo $e->getCode()." ".$e->getMessage();
}