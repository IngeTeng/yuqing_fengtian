<?php

/**
 * @filename uctoutiao.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2017-12-20
 * @updatetime 
 * @version 1.0
 * @Description
 * uc头条api抓取
 */

// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/app_list/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use \SPIDER_CENTER\APP_LIST\Configure;

$spider_site = 'uctoutiao';

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

$url_host = Configure::$url_hosts[$spider_site];        // 获取爬取网址

$clist = array('100', '323644874', '51830095', '1192652582');//推荐、汽车、热点、社会
$count = count($clist);
for($i = 0; $i < $count; $i++) {
    $spider_url = sprintf($url_host, $clist[$i]);       // 构造url
    $spider_result = get_html($spider_url);     // 获取html
    $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

    $content_request['list_id'] = $clist[$i];
    $content_request['timestamp'] = time();
    $content_request['collect_url'] = $spider_url; 

    handle_one_page($spider_result, $spider_result_path, $content_request, $log);
    sleep(Configure::SLEEP_TIME);
}
error:
    $log->INFO('爬取完毕');
