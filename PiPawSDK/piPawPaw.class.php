<?php

class piPawPay{
    function __construct() {

        // TODO - Insert your code here
    }

    function curl_get($Url){

        // is cURL installed yet?
        if (!function_exists('curl_init')){
            die('Sorry cURL is not installed!');
        }

        // OK cool - then let's create a new cURL resource handle
        $ch = curl_init();

        // Now set some options (most are optional)

        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $Url);

        // Set a referer
        curl_setopt($ch, CURLOPT_REFERER, "http://www.ugamehome.com/index.php");

        // User agent
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");

        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Download the given URL, and return output
        $output = curl_exec($ch);

        // Close the cURL resource, and free system resources
        curl_close($ch);

        return $output;
    }

    function curl_post_array($Url,$array){

        // is cURL installed yet?
        if (!function_exists('curl_init')){
            die('Sorry cURL is not installed!');
        }

        $curlPost = '';
        foreach($array as $key => $value)
        {
            $curlPost = $curlPost.$key."=".urlencode($value)."&";
        }
        $curlPost =  substr($curlPost, 0,-1);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$Url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $output = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Curl error: ' .  curl_error($ch);
        }
        curl_close($ch);

        return $output;
    }

    function curl_post_json($Url,$json){

        // is cURL installed yet?
        if (!function_exists('curl_init')){
            die('Sorry cURL is not installed!');
        }

        $curl_header = array('Content-Type: application/json','Content-Length: '.strlen($json));

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$Url);  			//curlopt_url
        curl_setopt($ch, CURLOPT_HEADER, $curl_header); //curlopt_header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	//curlopt_returntransfer
        curl_setopt($ch, CURLOPT_POST, 1);				//curlopt_post
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);	//curlopt_postfields
        $output = curl_exec($ch);
        $output = strstr($output, "{");
        if(curl_errno($ch))
        {
            echo 'Curl error: ' .  curl_error($ch);
        }
        curl_close($ch);

        return $output;
    }

    function curlByPost($json,$url){
        $rst = $this->curl_post_json($url, $json);
        return $rst;
    }

    function __destruct() {

        // TODO - Insert your code here
    }
}