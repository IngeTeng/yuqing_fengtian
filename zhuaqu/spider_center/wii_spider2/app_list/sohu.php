<?php

/**
 * @filename sohu.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-13  17:11:27
 * @updatetime 2016-8-13  17:11:27
 * @version 1.0
 * @Description
 * 搜狐api抓取
 * 
 */

// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/app_list/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use \SPIDER_CENTER\APP_LIST\Configure;

$spider_site = 'sohu';

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

$range = range(1, 50);
foreach($range as $num) {
    $spider_url = 'http://api.k.sohu.com/api/channel/v5/news.go?channelId='. $num. '&num=20&imgTag=1&showPic=1&picScale=11&rt=json&net=wifi&cdma_lat=34.178484&cdma_lng=108.957980&from=channel&page=1&action=0&mode=0&cursor=0&mainFocalId=0&viceFocalId=0&lastUpdateTime=0&p1=NTk0MTQwODE4ODIzMzg1NTA2OQ%3D%3D&gid=02ffff1106111167a3fdbecaf2a70ee9379eaf07cd1197&pid=-1';
    $spider_result = get_html($spider_url);     // 获取html
    $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

    $content_request['timestamp'] = time();
    $content_request['collect_url'] = $spider_url; 

    handle_one_page($spider_result, $spider_result_path, $content_request, $log);
    sleep(Configure::SLEEP_TIME);
}
error:
    $log->INFO('爬取完毕');
