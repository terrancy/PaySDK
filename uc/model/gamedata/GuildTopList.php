<?php
/**
 * 游戏中公会排行榜
 * Date: 15-12-15
 * Time: 下午2:18
 */

class GuildTopList extends AbstractDataContent {

    public $guildId;//必填  公会ID    每天一次
    public $guildName;//必填  公会名称
    public $guildRanking;//必填  排名
    public $leaderRoleName;//必填  公会会长的角色名称
    public $leaderAccountId;//必填  公会会长的账号ID
    public $zoneId;//区服ID
    public $zoneName;//区服名称

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
    public function getGuildRanking()
    {
        return $this->guildRanking;
    }

    /**
     * @param mixed $guildRanking
     */
    public function setGuildRanking($guildRanking)
    {
        $this->guildRanking = $guildRanking;
    }

    /**
     * @return mixed
     */
    public function getLeaderRoleName()
    {
        return $this->leaderRoleName;
    }

    /**
     * @param mixed $leaderRoleName
     */
    public function setLeaderRoleName($leaderRoleName)
    {
        $this->leaderRoleName = $leaderRoleName;
    }

    /**
     * @return mixed
     */
    public function getLeaderAccountId()
    {
        return $this->leaderAccountId;
    }

    /**
     * @param mixed $leaderAccountId
     */
    public function setLeaderAccountId($leaderAccountId)
    {
        $this->leaderAccountId = $leaderAccountId;
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
        if(!isset($this->guildRanking)){
            return false;
        }
        if(!isset($this->leaderRoleName)){
            return false;
        }
        if(!isset($this->leaderAccountId)){
            return false;
        }
        if(!isset($this->zoneId)){
            return false;
        }
        if(!isset($this->zoneName)){
            return false;
        }
        return true;
    }

}