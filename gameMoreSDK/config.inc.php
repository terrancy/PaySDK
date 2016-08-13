<?php
return array(
    //测试: http://test.youxiduo.com:11080/CheckDeliveryCode
    //正式: http://sdk.gamepaypay.com:11080/CheckDeliveryCode
    "urlVerify"             =>      "http://sdk.gamepaypay.com:11080/CheckDeliveryCode",
    "keyPrivateRsa"     =>      dirname(__FILE__)."/key/rsa_private_key.pem",
    "keyPublicRsa"      =>      dirname(__FILE__)."/key/rsa_public_key.pem",
);