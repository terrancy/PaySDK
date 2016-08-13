<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/26
 * Time: 18:01
 */
$str = 'transdata=%7B%22appid%22%3A%22500000185%22%2C%22count%22%3A1%2C%22cporderid%22%3A%22lrtest5bf98ef3-6543-4d2d-9f3f-1cadfda3fc0e%22%2C%22cpprivate%22%3A%22%22%2C%22feetype%22%3A2%2C%22money%22%3A1%2C%22paytype%22%3A5%2C%22result%22%3A%220%22%2C%22transid%22%3A%2232011407211739380639%22%2C%22transtime%22%3A%222014-07-21+17%3A39%3A38%22%2C%22transtype%22%3A0%2C%22waresid%22%3A1%7D&sign=OvL34IZYWbHJXUyXXBRSgZjeqoxPK8Bh68UArbDbd9mf0LZDYFwiIuB6Vqt4MtiphCOJN9cNr4aJzY0cCaq1CxO6PABmyMf37umL5uOro58NcoImKHIhxtaHenPGlY46aaf6GgGagY4MqY%2BjEdbFlbJQh4Y%2BdvHkUKjJVyEdQsE%3D&signtype=RSA';


parse_str($str,$arrData);
$json = json_encode($arrData);
//echo $json;
//var_dump($arrData);

//echo $arrData['transdata'];

$str = '{"transdata":"{\"appid\":\"5000100626\",\"appuserid\":\"\u84dd\u8272\u7684\u6d1b\u514b\u8d1d\",\"cporderid\":\"29680\",\"cpprivate\":\"8002\",\"currency\":\"RMB\",\"feetype\":2,\"money\":1.00,\"paytype\":401,\"result\":0,\"transid\":\"32061607261652401366\",\"transtime\":\"2016-07-26 16:53:51\",\"transtype\":0,\"waresid\":2}","sign":"VafvV848I6uuKeRAypSX2m9zMnTek83qkcKSg9HWDZOwipZ\/hnlU7nX20cPTosMudVt5NtAJo+5kIekRvdr81\/LUvz0pgLUv5EN9hW6\/cEhb5OqT5xTKECUddHyeMWAzw36EICBR+beDW7o+Wz\/QYsNeQ6x42Fd6u97L645THYQ=","signtype":"RSA"}';

$arrData = json_decode($str,true);

var_dump($arrData);
