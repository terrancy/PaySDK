<?php

class downJoySDK{
    private $_idApp = "48";
    private $_keyApp = "iCpsVevxK42i";
    private $dirLog;

    function __construct($idAPP="",$keyApp=""){
        $this->payAnySDKInit($idAPP,$keyApp);
        $this->getLogInit();
    }

    function getLogInit(){
        if($this->dirLog == ""){
            $this->dirLog = dirname(__FILE__)."/log/downJoySDK.txt";
        }
    }

    function payAnySDKInit($idAPP="",$keyApp=""){
        if(!empty($idAPP)){
            $this->_idApp = $idAPP;
        }
        if(!empty($keyApp)){
            $this->_keyApp = $keyApp;
        }
    }

    function checkSign($arrData){
        $arrRst = "failure";
        $signAuth = $this->getSign($arrData);
        if(!empty($signAuth) && ($signAuth == $arrData['sign'])) $arrRst = 'success';
        return $arrRst;
    }

    function getSign($arrData){
        $signAuth = "";
        //{"point":"2016_0_ngJRRFMx","seqId":"482016032899w48z49","transNo":"9","time":"1459133312315","pf":"1","sign":"e8bd89d43152ceb60655fd6ded954adb"}
        if(!empty($arrData['point']) && !empty($arrData['time']) && !empty($arrData['seqId'])){
            $arrSign = array(
                "appid"     =>      $this->_idApp,
                "point"     =>      $arrData['point'],
                "time"     =>      $arrData['time'],
                "paymentKey"    =>      $this->_keyApp,
                "seqId"     =>      $arrData['seqId'],
            );
            file_put_contents($this->dirLog,date('Y-m-d H:i:s')."(arrSign):".implode("|",$arrSign).PHP_EOL,FILE_APPEND);
           $signAuth = md5(implode("|",$arrSign));
            file_put_contents($this->dirLog,date('Y-m-d H:i:s')."(signAuth):".$signAuth.PHP_EOL,FILE_APPEND);
        }
        return $signAuth;
    }

}
