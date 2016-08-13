<?php
/**
 * 自定义异常信息
 * <br>
 */
class SDKException extends Exception {
    public function __construct($message, $code = -1) {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__.':['.$this->code.']:'.$this->message.'\n';
    }

    public function customFunction() {
        echo '自定义错误类型';
    }

}