<?php
require_once dirname(dirname(__FILE__)).'/service/SDKServerService.php';
require_once dirname(dirname(__FILE__)).'/model/SDKException.php';
require_once dirname(dirname(__FILE__)).'/constant/GameDataCategory.php';
require_once dirname(dirname(__FILE__)).'/model/gamedata/GameData.php';
require_once dirname(dirname(__FILE__)).'/model/gamedata/GuildTopList.php';
require_once dirname(dirname(__FILE__)).'/model/gamedata/LoginGameRole.php';
require_once dirname(dirname(__FILE__)).'/model/gamedata/RoleLevelTopList.php';
require_once dirname(dirname(__FILE__)).'/model/gamedata/RolePowerTopList.php';
require_once dirname(dirname(__FILE__)).'/model/gamedata/UserInfo.php';

//玩家的accountId
$accountId = "02592edac3d93be60af0f3665034e7d5";

//=========以下5类数据，可以一起传，也可以分开传，也可以部分合并传=========
//==================================================================

/**
* 必接功能<br>
* 提交游戏扩展数据功能，游戏SDK要求游戏在运行过程中，提交一些用于运营需要的扩展数据，这些数据通过扩展数据提交方法进行提交。
* 登录游戏角色成功后调用，及角色等级变化后调用
* 游戏内如果没有相应的字段：int传-1，string传"不存在"
*/
//玩家的游戏数据,content内参数必填
$loginGameRole = new LoginGameRole();
$loginGameRole->setRoleLevel("88");
$loginGameRole->setRoleName("请∝再给我一支烟");
$loginGameRole->setZoneName("终南山下-兵临城下");
$loginGameRole->setRoleId("53568193");
$loginGameRole->setZoneId("2705");
$loginGameRole->setRoleCTime(1453355744);
$loginGameRole->setOs("android");
$loginGameRole->setRoleLevelMTime(1453355744);

//构造玩家的游戏数据对象,构建数据后需要调SDKServerService::gameData接口提交数据,详见文档最后
$roleData = new GameData(GameDataCategory::$LOGIN_GAME_ROLE, $loginGameRole);

//==================================================================
/**
 * userInfo为用户个人信息，选接
 */
//用户个人信息,content内参数必填
$userInfo1 = new UserInfo();
$userInfo1->setGuildId("101");
$userInfo1->setGuildName("星际联盟");
$userInfo1->setGuildLevel(38);
$userInfo1->setGuildLeader("a1b2c3d4e5f69876543210c0e9b2");
$userInfo1->setZoneId("1");
$userInfo1->setZoneName("海1区");
$userInfo1->setPower(2230);
$userInfo1->setOs("android");
$userInfo1->setRoleId("53568193");
$userInfo1->setRoleName("请∝再给我一支烟");

$userInfo2 = new UserInfo();
$userInfo2->setGuildId("101");
$userInfo2->setGuildName("星际联盟");
$userInfo2->setGuildLevel(38);
$userInfo2->setGuildLeader("a1b2c3d4e5f69876543210c0e9b2");
$userInfo2->setZoneId("1");
$userInfo2->setZoneName("海1区");
$userInfo2->setPower(2230);
$userInfo2->setOs("android");
$userInfo2->setRoleId("53568194");
$userInfo2->setRoleName("霸王");

//合并两个数据
$userInfoContent = array($userInfo1, $userInfo2);

//构造玩家的游戏数据对象
$userInfoData = new GameData(GameDataCategory::$USER_INFO, $userInfoContent);

//==================================================================
/**
 * roleLevelTopList为游戏中角色等级排行榜，选接
 */
//游戏中角色等级排行榜,content内参数必填
$roleLevelContent1 = new RoleLevelTopList();
$roleLevelContent1->setZoneId("1");
$roleLevelContent1->setZoneName("海1区");
$roleLevelContent1->setRoleId("10023");
$roleLevelContent1->setRoleName("霸王");
$roleLevelContent1->setRoleLevel("60");
$roleLevelContent1->setRoleRanking(1);
$roleLevelContent1->setAccountId("a1b2c3897621876543210c0e97");

$roleLevelContent2 = new RoleLevelTopList();
$roleLevelContent2->setZoneId("1");
$roleLevelContent2->setZoneName("海1区");
$roleLevelContent2->setRoleId("9821");
$roleLevelContent2->setRoleName("星空无敌");
$roleLevelContent2->setRoleLevel("59");
$roleLevelContent2->setRoleRanking(2);
$roleLevelContent2->setAccountId("b1b2c3897621876543210c0ee5");

//合并两个数据,content内参数必填
$roleLevelContent = array($roleLevelContent1, $roleLevelContent2);

//构造玩家的游戏数据对象
$roleLevelTopData = new GameData(GameDataCategory::$ROLELEVEL_TOPLIST, $roleLevelContent);

//==================================================================
/**
 * rolePowerTopList为游戏中角色战力排行榜，选接
 */
//游戏中角色战力排行榜,content内参数必填
$rolePowerContent1 = new RolePowerTopList();
$rolePowerContent1->setZoneId("1");
$rolePowerContent1->setZoneName("海1区");
$rolePowerContent1->setRoleId("10017");
$rolePowerContent1->setRoleName("冲天大炮");
$rolePowerContent1->setPower(65535);
$rolePowerContent1->setPowerRanking(1);
$rolePowerContent1->setAccountId("a5b2c3890a21876543210c0e9c");

$rolePowerContent2 = new RolePowerTopList();
$rolePowerContent2->setZoneId("1");
$rolePowerContent2->setZoneName("海1区");
$rolePowerContent2->setRoleId("10023");
$rolePowerContent2->setRoleName("霸王");
$rolePowerContent2->setPower(65530);
$rolePowerContent2->setPowerRanking(2);
$rolePowerContent2->setAccountId("a1b2c3897621876543210c0e97");

//合并两个数据
$rolePowerContent = array($rolePowerContent1, $rolePowerContent2);

//构造玩家的游戏数据对象
$rolePowerTopData = new GameData(GameDataCategory::$ROLEPOWER_TOPLIST, $rolePowerContent);

//==================================================================
/**
 * guildTopList为游戏中公会排行榜，选接
 */
//游戏中工会排行榜,content内参数必填
$guildContent1 = new GuildTopList();
$guildContent1->setGuildId("101");
$guildContent1->setGuildName("星际联盟");
$guildContent1->setGuildRanking(1);
$guildContent1->setLeaderRoleName("我是谁");
$guildContent1->setLeaderAccountId("e0b6c3897621876543210c0ec2");
$guildContent1->setZoneId("1");
$guildContent1->setZoneName("海1区");

$guildContent2 = new GuildTopList();
$guildContent2->setGuildId("109");
$guildContent2->setGuildName("第一中队");
$guildContent2->setGuildRanking(2);
$guildContent2->setLeaderRoleName("老大");
$guildContent2->setLeaderAccountId("c3b6c3897621876543210c0e06");
$guildContent2->setZoneId("1");
$guildContent2->setZoneName("海1区");

//合并两个数据
$guildContent = array($guildContent1, $guildContent2);

//构造玩家的游戏数据对象
$guildTopData = new GameData(GameDataCategory::$GUILD_TOP_LIST, $guildContent);
//==================================================================

//构造玩家的游戏数据对象
$gameData = array($roleData, $userInfoData, $roleLevelTopData, $rolePowerTopData, $guildTopData);

/**
 * ===========================
 * 构造完数据后，需要调用SDKServerService::gameData($accountId, $gameData)方法，才会将数据上传到服务器
 * ===========================
 */
try{
    $result = SDKServerService::gameData($accountId, $gameData);
    if($result){echo "上传成功";};
}
catch (SDKException $e){
    echo $e->getCode()." ".$e->getMessage();
}
