<?php

class weChatSDK{

    private $dirWeChat= "";
    private $unifiedOrder;

    function __construct(){
        $this->dirWeChat = dirname(__FILE__)."/weChat";
    }

    function getOrderUnifiedInit(){
        require $this->dirWeChat."/WxPayPubHelper.php";
        $this->unifiedOrder = new UnifiedOrder_pub();
    }

    function orderUnified($arrConfig){
        $this->getOrderUnifiedInit();
        $this->unifiedOrder->setParameter("body",$arrConfig['body']);//商品描述
        $timeStamp = time();
        $totalFee = $arrConfig['total_fee'];
        $attach = json_encode($arrConfig['attach']);
        $out_trade_no = WxPayConf_pub::APPID."$timeStamp";
        $this->unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
        $this->unifiedOrder->setParameter("total_fee",$totalFee);//总金额
        $this->unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址
        $this->unifiedOrder->setParameter("trade_type","APP");//交易类型
        $this->unifiedOrder->setParameter("attach",$attach);//附加数据
        $arrUnifiedOrder = $this->unifiedOrder->getResultUnifiedOrder();
        $arrUnifiedOrder['out_trade_no'] = $out_trade_no;
        return $arrUnifiedOrder;
    }

    function getWeChatSign($Obj){
        $sign = $this->getSign($Obj);
        return $sign;
    }

    /**
     * 	作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if($urlencode) {
                $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar ="";
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /**
     * 	作用：生成签名
     */
    public function getSign($Obj){
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".WxPayConf_pub::KEY;
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }

}