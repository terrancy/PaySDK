<?php
require_once dirname(__FILE__).'/AbstractDataContent.php';
/**
 * 玩家游戏数据对象定义
 * Date: 15-12-15
 * Time: 下午1:47
 */

class GameData {

    public $category; //标示游戏类别数据

    public $content;//具体内容，可能为列表，也可能为单个对象

    function __construct($category, $content)
    {
        $this->category = $category;
        $this->content = $content;
    }



    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }



    /**
     * 对外的数据校验方法
     * @return
     */
    public function validate() {
        if(empty($this->category)){
            return false;
        }

        if($this->content == null){
            return false;
        }

        if(is_array($this->content)){
            //循环判断
            $checkResult = true;
            foreach ($this->content as $obj){
                if($obj instanceof AbstractDataContent){
                    $checkResult &= $obj->validate();
                }
                else{
                    return false;
                }

                //列表判断时，只要有一个出现false,无需判断后续的数据
                if(!$checkResult){
                    return $checkResult;
                }
            }
            return $checkResult;

        }
        else if($this->content instanceof AbstractDataContent){
            if($this->content instanceof AbstractDataContent){
                return $this->content->validate();
            }
            else{
                return false;
            }
        }

        //数据类型不明确，一律返回校验不通过
        return false;
    }


}