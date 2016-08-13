<?php
/**
 * 游戏数据基础类
 * Date: 15-12-15
 * Time: 下午1:59
 * 备注：为了简便实现json_encode的功能，其子类的属性全部是public
 */
abstract class AbstractDataContent {

    /**
     * 必填参数校验
     * @return
     * true:校验通过    false:有参数为空
     */
    public abstract function validate();
}