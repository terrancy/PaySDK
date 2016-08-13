<?php
/**
 * 游戏角色及区服数据
 * Date: 15-12-15
 * Time: 下午2:30
 */

class LoginGameRole extends AbstractDataContent {
    public $zoneId;//必填  区服ID    用户登录游戏成功后
    public $zoneName;//必填  区服名称
    public $roleId;//必填  角色ID
    public $roleName;//必填  角色昵称
    public $roleLevel;//必填  角色等级
    public $roleCTime;//必填  角色创建时间(单位：秒)
    public $os;//必填  游戏平台 android或者ios，小写字母。默认为android
    public $roleLevelMTime;//非必填  角色等级变化时间(单位：秒)

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
     * @return mixed
     */
    public function getRoleLevel()
    {
        return $this->roleLevel;
    }

    /**
     * @param mixed $roleLevel
     */
    public function setRoleLevel($roleLevel)
    {
        $this->roleLevel = $roleLevel;
    }

    /**
     * @return mixed
     */
    public function getRoleCTime()
    {
        return $this->roleCTime;
    }

    /**
     * @param mixed $roleCTime
     */
    public function setRoleCTime($roleCTime)
    {
        $this->roleCTime = $roleCTime;
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
    public function getRoleLevelMTime()
    {
        return $this->roleLevelMTime;
    }

    /**
     * @param mixed $roleLevelMTime
     */
    public function setRoleLevelMTime($roleLevelMTime)
    {
        $this->roleLevelMTime = $roleLevelMTime;
    }



    /**
     * 必填参数校验
     * @return
     * true:校验通过    false:有参数为空
     */
    public function validate()
    {
        if(!isset($this->zoneId)){
            return false;
        }
        if(!isset($this->zoneName)){
            return false;
        }
        if(!isset($this->roleId)){
            return false;
        }
        if(!isset($this->roleName)){
            return false;
        }
        if(!isset($this->roleLevel)){
            return false;
        }
        if(!isset($this->os)){
            return false;
        }
        if(!$this->checkOs($this->os)){
            return false;
        }
        if(!isset($this->roleCTime)){
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