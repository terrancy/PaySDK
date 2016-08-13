<?php
/**
 * 用户个人信息
 * Date: 15-12-15
 * Time: 下午2:31
 */

class UserInfo extends AbstractDataContent {
    public $guildId;//必填  用户加入的公会的ID  用户登录游戏成功后
    public $guildName;//必填  用户加入的公会的名称
    public $guildLevel;//必填  用户加入的公会的等级
    public $guildLeader;//必填 用户加入的公会的会长的账号ID
    public $zoneId;//必填 区服ID
    public $zoneName;//必填 区服名称
    public $power;//必填  个人战力值
    public $os;//必填  游戏平台 android或者ios，小写字母。默认为android
    public $roleId;//非必填 角色ID
    public $roleName;//非必填 角色昵称

    /**
     * @return mixed
     */
    public function getGuildId()
    {
        return $this->guildId;
    }

    /**
     * @param mixed $guildId
     */
    public function setGuildId($guildId)
    {
        $this->guildId = $guildId;
    }

    /**
     * @return mixed
     */
    public function getGuildName()
    {
        return $this->guildName;
    }

    /**
     * @param mixed $guildName
     */
    public function setGuildName($guildName)
    {
        $this->guildName = $guildName;
    }

    /**
     * @return mixed
     */
    public function getGuildLevel()
    {
        return $this->guildLevel;
    }

    /**
     * @param mixed $guildLevel
     */
    public function setGuildLevel($guildLevel)
    {
        $this->guildLevel = $guildLevel;
    }

    /**
     * @return mixed
     */
    public function getGuildLeader()
    {
        return $this->guildLeader;
    }

    /**
     * @param mixed $guildLeader
     */
    public function setGuildLeader($guildLeader)
    {
        $this->guildLeader = $guildLeader;
    }

    /**
     * @return mixed
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @param mixed $power
     */
    public function setPower($power)
    {
        $this->power = $power;
    }

    /**
     * @return mixed
     */
    public function getZoneId()
    {
        return $this->zoneId;
    }

    /**
     * @param mixed $zoneId
     */
    public function setZoneId($zoneId)
    {
        $this->zoneId = $zoneId;
    }

    /**
     * @return mixed
     */
    public function getZoneName()
    {
        return $this->zoneName;
    }

    /**
     * @param mixed $zoneName
     */
    public function setZoneName($zoneName)
    {
        $this->zoneName = $zoneName;
    }

    /**
     * @return mixed
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @param mixed $os
     */
    public function setOs($os)
    {
        $this->os = $os;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @param mixed $roleId
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * @return mixed
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * @param mixed $roleName
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;
    }




    /**
     * 必填参数校验
     * @return
     * true:校验通过    false:有参数为空
     */
    public function validate()
    {
        if(!isset($this->guildId)){
            return false;
        }
        if(!isset($this->guildName)){
            return false;
        }
        if(!isset($this->guildLevel)){
            return false;
        }
        if(!isset($this->guildLeader)){
            return false;
        }
        if(!isset($this->zoneId)){
            return false;
        }
        if(!isset($this->zoneName)){
            return false;
        }
        if(!isset($this->power)){
            return false;
        }
        if(!$this->checkOs($this->os)){
            return false;
        }
        return true;
    }

    private function checkOs($roleOs){
        $osName = array("android", "ios");
        foreach ($osName as $tempOs) {
            if($roleOs == $tempOs){
                return true;
            }
        }
        return false;
    }
}