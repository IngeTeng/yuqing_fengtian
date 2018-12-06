<?php

/**
 * @filename zaker.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  16:27:22
 * @updatetime 2016-8-15  16:27:22
 * @version 1.0
 * @Description
 * ZAKER新闻客户端api抓取
 * 
 */

// 引入配置文件和一些工具类 常用函数
require('../config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/app_list/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use \SPIDER_CENTER\APP_LIST\Configure;

$spider_site = 'zaker';

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site), LOG_LEVEL);

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

$clist = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13, 14, 15, 11645, 11646, 11648);
$count = count($clist);
for($i = 0; $i < $count; $i++) {
    if($clist[$i] < 16) {
        $spider_url = sprintf(Configure::$url_hosts['zaker1'], $clist[$i]);
    }
    else {
        $spider_url = sprintf(Configure::$url_hosts['zaker2'], $clist[$i]);
    }
    $the_response = get_html($spider_url);     // 获取html
    @$spider_result = gzdecode($the_response);       // 解压
    if(empty($spider_result)) {
        $log->WARN("zaker内容抓取出错, c_id 为: {$clist[$i]}; Response: $the_response");
        continue;
    }
    
    $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

    $content_request['timestamp'] = time();
    $content_request['collect_url'] = $spider_url; 

    handle_one_page($spider_result, $spider_result_path, $content_request, $log);
    sleep(Configure::SLEEP_TIME);
}
error:
    $log->INFO('爬取完毕');

