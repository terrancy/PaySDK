<?php
/**
 * 游戏中角色战力排行榜
 * Date: 15-12-15
 * Time: 下午2:31
 */

class RolePowerTopList extends AbstractDataContent {
    public $zoneId;//必填  区服ID    每天一次
    public $zoneName;//必填  区服名称
    public $roleId;//必填  角色ID
    public $roleName;//必填  角色昵称
    public $power;//必填  战力
    public $powerRanking;//必填  玩家在混服战力榜单中的排名
    public $accountId;//必填  账号ID

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
    public function getPowerRanking()
    {
        return $this->powerRanking;
    }

    /**
     * @param mixed $powerRanking
     */
    public function setPowerRanking($powerRanking)
    {
        $this->powerRanking = $powerRanking;
    }

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
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
        if(!isset($this->power)){
            return false;
        }
        if(!isset($this->powerRanking)){
            return false;
        }
        if(!isset($this->accountId)){
            return false;
        }
        return true;
    }


}