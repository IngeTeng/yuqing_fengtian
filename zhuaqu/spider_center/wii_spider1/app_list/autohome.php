<?php

/**
 * @filename auto.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-14  17:29:46
 * @updatetime 2016-8-14  17:29:46
 * @version 1.0
 * @Description
 * 汽车之家api
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

$spider_site = 'autohome';

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

$clist = array(
    'news_v8.7.0/news/orinews-pm2-l0-focus0-v8.7.5.json', //原创
    //'news_v8.7.0/news/newstopiclist.ashx?pm=2&lastid=0&count=100&typeid=0', //话题
    'news_v8.7.0/news/getimageinfo.ashx?pm=2&type=0&lastid=0&pageindex=1&pagesize=100&busitype=0', //图说
    );
$count = count($clist);
for($i = 0; $i < $count; $i++) {
    $spider_url = sprintf($url_host, $clist[$i]);       // 构造url
    $spider_result = get_html($spider_url);     // 获取html
    $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

    // 保存抓取结果
    if(!is_dir($spider_result_path)) {
        mkdir($spider_result_path);
    }
    $content_request['timestamp'] = time();
    $content_request['collect_url'] = $spider_url; 
    
    handle_one_page($spider_result, $spider_result_path, $content_request, $log);
    sleep(Configure::SLEEP_TIME);
}
error:
    $log->INFO('爬取完毕');
