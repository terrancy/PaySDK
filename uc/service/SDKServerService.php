<?php
require_once dirname(__FILE__).'/BaseSDKService.php';
require_once dirname(dirname(__FILE__)).'/util/LoggerHelper.php';
require_once dirname(dirname(__FILE__)).'/constant/ServiceName.php';

require_once dirname(dirname(__FILE__)).'/model/SidInfo.php';
require_once dirname(dirname(__FILE__)).'/model/SessionInfo.php';
require_once dirname(dirname(__FILE__)).'/model/gamedata/GameData.php';


/**
 * 九游服务端有关的通信服务
 * Class SDKServerService
 */
class SDKServerService extends BaseSDKService{

    /**
     * 用户会话验证接口-支持统一账号标识(account.verifySession)
     * @param sid
     * @return
     * @throws SDKException
     */
    public static function verifySession($sid) {
        try{
            $params = array();
            $params["sid"] = $sid;
            $result = parent::getSDKServerResponse($params, ServiceName::$CP_PREFIX, ServiceName::$VERIFYSESSION_SERVICE);
            $sessionInfo = new SessionInfo();
            $sessionInfo->nickName = $result["nickName"];
            $sessionInfo->accountId = $result["accountId"];
            $sessionInfo->creator = $result["creator"];
        }
        catch(SDKException $e){
            throw $e;
        }
        return $sessionInfo;
    }

    /**
     * 接收游戏服务器提交的游戏角色数据接口(ucid.game.gameData)
     * <br>
     * 请优先使用sdk客户端版接口，不推荐用服务器版接口
     * @param sid
     * @param gameData
     * @return
     * @throws SDKException
     */
    public static function gameData($accountId, array $gameData) {

        $categoryArray = array();

        if(empty($gameData)){
            throw new SDKException("游戏服务器角色数据类型为空", 10);
        }

        //遍历校验游戏参数
        foreach ($gameData as $model){
            if($model instanceof GameData){
                array_push($categoryArray, $model->getCategory());
                if(!$model->validate()){
                    throw new SDKException("游戏数据有必填值为空", 10);
                }
            }
            else{
                throw new SDKException("游戏数据类型设置错误,非GameData", 10);
            }
        }

        //确定是否有必填的参数校验(下面个人信息类型，必须在sid和accountId中选填一个)
        if(in_array(GameDataCategory::$LOGIN_GAME_ROLE, $categoryArray) || in_array(GameDataCategory::$USER_INFO, $categoryArray)){
            if(!isset($accountId)){
                throw new SDKException("玩家类(非榜单)游戏数据，用户标示accountId为必填", 10);
            }
        }

        //转换游戏参数
        $gameDataStr = null;
        try {
            $gameDataStr = json_encode($gameData);
            $gameDataStr = urlencode($gameDataStr);
        } catch (Exception $e) {
            throw new SDKException("JsonEncode游戏服务器角色数据有误", -1);
        }

        try{
            $params = array();
            $params["accountId"] = $accountId;

            $params["gameData"] = $gameDataStr;
            $result = parent::getSDKGameDataResponse($params, ServiceName::$CP_GAMEDATA_PREFIX, ServiceName::$GAMEDATA_SERVICE);
        }
        catch(SDKException $e){
            throw $e;
        }
        return empty($result) ? true : false;
    }

    /**
     * 用户会话验证接口(ucid.user.sidInfo)
     * 仅供已接入过的老游戏使用
     * @param sid
     * @return
     * @throws SDKException
     */
    public static function verifySid($sid){
        try{
            $params = array();
            $params["sid"] = $sid;
            $result = parent::getSDKServerResponse($params, ServiceName::$SS_PREFIX, ServiceName::$SIDINFO_SERVICE);
            $sidInfo = new SidInfo();
            $sidInfo->nickName = $result["nickName"];
            $sidInfo->ucid = $result["ucid"];
        }
        catch(SDKException $e){
            throw $e;
        }
        return $sidInfo;
    }

}