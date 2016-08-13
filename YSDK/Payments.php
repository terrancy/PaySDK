<?php

/**
 *
 * 查询游戏币余额
 *
 * @param object $sdk YSDK Object
 * @param array $params params
 * @param array $cookie cookie params
 *
 * @return array
 *
 *
 */

function get_balance_m($sdk, $params,$accout_type){
    $method="get";
    $script_name = '/mpay/get_balance_m';    
    $protocol ='https';
    return $sdk->api_pay($script_name, $accout_type,$params,$method,$protocol);
}


function pay_m($sdk, $params,$accout_type){
    $method="post";
    $script_name = '/mpay/pay_m';
//    $cookie["org_loc"] = urlencode($script_name);
    $protocol ='https';
    return $sdk->api_pay($script_name, $accout_type,$params,$method,$protocol);
}


function present_m($sdk, $params, $accout_type){
    $method="post";
    $script_name = '/mpay/present_m';
    $cookie["org_loc"] = urlencode($script_name);
    $protocol ='https';
    return $sdk->api_pay($script_name,$accout_type,$params,$method,$protocol);
}

