<?php

/**
 * @filename tencent.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  14:18:36
 * @updatetime 2016-8-15  14:18:36
 * @version 1.0
 * @Description
 * 腾讯手机客户端
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

$spider_site = 'tencent';

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

$clist = array('news_news_top', 'news_news_ent', 'news_news_xian', 'news_news_sports', 'news_news_finance', 'news_news_mil', 'news_news_tech', 'news_news_ssh', 'news_news_lad', 'news_news_auto');
$count = count($clist);
for($i = 0; $i < $count; $i++) {
    $spider_url = "http://r.inews.qq.com/getQQNewsIndexAndItems?uid=d59e160a-fd48-4319-92c9-76a3942e048f&Cookie=%20lskey%3D%3B%20luin%3D%3B%20logintype%3D0%20&qn-rid=1398556242&store=109&hw=Xiaomi_MI2&devid=860308025820347&qn-sig=86efba44d8dfbdabeea18acca9d9b483&screen_width=720&mac=ac%253Af7%253Af3%253A1e%253Af9%253Ae0&chlid={$clist[$i]}&appver=19_android_4.4.3&qqnetwork=wifi&sceneid=73387&imsi=460019328236769&apptype=android";
    $spider_result = get_html($spider_url);     // 获取html
    $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

    $content_request['timestamp'] = time();
    $content_request['collect_url'] = $spider_url; 

    handle_one_page($spider_result, $spider_result_path, $content_request, $log);
    sleep(Configure::SLEEP_TIME);
}
error:
    $log->INFO('爬取完毕');
