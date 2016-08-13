
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
</head>

<?php
/*
    *功能：下单服务器异步通知页面
    *版本：1.0
    *日期：2014-06-26
    *说明：
    *以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
    *该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
*/


require_once("config.php");
require_once("base.php");

    //获取notifyData，需要添加notifyData=
    $notifyData= 'transdata=%7B%22appid%22%3A%22500000185%22%2C%22count%22%3A1%2C%22cporderid%22%3A%22lrtest5bf98ef3-6543-4d2d-9f3f-1cadfda3fc0e%22%2C%22cpprivate%22%3A%22%22%2C%22feetype%22%3A2%2C%22money%22%3A1%2C%22paytype%22%3A5%2C%22result%22%3A%220%22%2C%22transid%22%3A%2232011407211739380639%22%2C%22transtime%22%3A%222014-07-21+17%3A39%3A38%22%2C%22transtype%22%3A0%2C%22waresid%22%3A1%7D&sign=OvL34IZYWbHJXUyXXBRSgZjeqoxPK8Bh68UArbDbd9mf0LZDYFwiIuB6Vqt4MtiphCOJN9cNr4aJzY0cCaq1CxO6PABmyMf37umL5uOro58NcoImKHIhxtaHenPGlY46aaf6GgGagY4MqY%2BjEdbFlbJQh4Y%2BdvHkUKjJVyEdQsE%3D&signtype=RSA';

    echo parseResp($notifyData, $platpkey, $notifyJson);
//    if(!parseResp($notifyData, $platpkey, $notifyJson)) {
//        echo "fail";
//    }
//
//    print_r($notifyJson);


?>