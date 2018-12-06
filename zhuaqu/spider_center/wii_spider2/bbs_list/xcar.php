<?php

/**
 * @filename xcar.php 
 * @encoding UTF-8 
 * @author WiiPu yjl
 * @createtime 2016-12-16  14:22:31
 * @updatetime 2016-12-16  14:22:31
 * @version 1.0
 * @Description
 * 爱卡汽车论坛搜索
 * 
 */


// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/bbs_list/config.php');
require(BASE_PATH. '/include/log.php');

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/xcar_log.txt'); //将出错信息输出到一个文本文件 

// 调用命名空间
use SPIDER_CENTER\BBS_LIST\Configure;

$spider_site = 'xcar';
$spider_name = $spider_site. '_'. Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND;

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site), LOG_LEVEL);
// 获取关键字
$task_response = get_keywords($spider_name, COLLECT_NAME);      
$task_array = json_decode($task_response, true);
if($task_array['status'] != REQUEST_OK) {
    $log->WARN("{$task_array['status']}  获取关键字失败, spider=$spider_name");
    goto error;
}

// 初始内容中心化请求
$content_request = array(
    "timestamp" => '',
    "collect_name" => COLLECT_NAME,
    "collect_ip" => COLLECT_HOST,
    "collect_kind" => Configure::COLLECT_KIND,
    "collect_from_site" => $spider_site,
    "collect_content_kind" => Configure::COLLECT_CONTENT_KIND,
    "collect_download_url" => "",
    "collect_url" => "",
    "keyword" => "",
    "spider_kind" => Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND,
);

$url_host = Configure::$url_hosts[$spider_site];        // 获取爬取网址
$cookie = 'place_prid=10; place_crid=433; place_ip=113.140.11.126_1; _Xdwnewuv=1; _PVXuv=592b5509bf030; _fwck_www=38c48099fc27b814cc1fd3df30ac964b; _appuv_www=45700702ce96664cb2c1841bddce837d; BIGipServerpool-c26-xcar-data-80=2378764042.20480.0000; _Xdwuv=594b5507d1a73; _fwck_tools=f0bd9ec8cef727d61c2fc31fb8c2aaf9; _appuv_tools=c0fdf9e74b4d38014cc968508e2ebdee; _locationInfo_=%7Burl%3A%22h%22%2Ccity_id%3A%22475%22%2Cprovince_id%3A%221%22%2C%20city_name%3A%22%25E5%258C%2597%25E4%25BA%25AC%22%7D; bbs_visitedfid=478D1825; Hm_lvt_53eb54d089f7b5dd4ae2927686b183e0=1508597668; Hm_lpvt_53eb54d089f7b5dd4ae2927686b183e0=1508600383; bbs_sid=UmFwx6';


// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $key_word = $keyword['keyword'];
    
    $spider_url = sprintf($url_host, urlencode($key_word), urlencode('一天内'));        // 构造url
    $spider_result = get_html($spider_url, $cookie);     // 获取html
    //转换编码，防止数据结果文件中的中文为乱码
    $encode = mb_detect_encoding($spider_result, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); 
    $spider_result = mb_convert_encoding($spider_result, 'UTF-8', $encode);
    $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径
    
    $content_request['timestamp'] = time();
    $content_request['collect_url'] = $spider_url; 
    $content_request['keyword'] = $key_word;
    
    handle_one_page($spider_result, $spider_result_path, $content_request, $log);
    sleep(Configure::SLEEP_TIME);
}
error:
    $log->INFO('爬取完毕');
    exit(0);
