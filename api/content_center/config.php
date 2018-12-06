<?php

/**
 * @filename setting.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @datetime 2016-6-3  14:28:50
 * @version 1.0
 * @Description
 * 
 */

// log级别配置
define("LOG_LEVEL", 15);

// 时区设置
date_default_timezone_set("PRC");

// 当前路径
define("BASE_PATH", str_replace("\\", "/", realpath(dirname(__FILE__))));
// 日志保存路径
define("LOG_HANDLER", BASE_PATH. '/log/'. date('Y_m_d'));
// 是否保存结果, 1为保存, 0不保存
define('SAVE_RESULT', 0);
// result保存路径
if(SAVE_RESULT == 1) {
    define("RESULT_PATH", BASE_PATH. '/result');
}
// secret配置
define("SECRET", "secret=3723905834958739587349857938292");

// 错误报告等级
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 正则表达式的配置
ini_set('pcre.backtrack_limit', 999999999);
ini_set('pcre.recursion_limit', 999999);
ini_set('memory_limit', '64M');





// 获取的html,带模拟登陆
function get_html($url, $cookie='', $proxy='', $proxy_port='', $referer='') {
    $ch = curl_init();
    // 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);      // 60秒超时

    if($cookie != '') {
        $coo = "Cookie:$cookie";
        $headers[] = $coo;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($referer != '') {
        curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
    if($proxy != '' and $proxy_port != '') {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    
    // 获取内容
    $output = curl_exec($ch);
    //$output = iconv("gb2312", "utf-8//IGNORE",$output);
    curl_close($ch);
    return $output;
}

