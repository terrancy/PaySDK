<?php

/**
 *
 * 验证登录票据是否有效
 *
 * @param object $sdk YSDK Object
 * @param array $params
 *
 * @return array
 *
 *
 */
function qq_check_token($sdk, $params){
    $method = 'get';
    $script_name = '/auth/qq_check_token';

    return $sdk->api_ysdk($script_name, $params, $method);
}

/**
 *
 * 验证授权凭证(access_token)是否有效
 *
 * @param object $sdk YSDK Object
 * @param array $params
 *
 * @return array
 *
 *
 */
function wx_check_token($sdk, $params){
    $method = 'get';
    $script_name = '/auth/wx_check_token';

    return $sdk->api_ysdk($script_name, $params, $method);
}

