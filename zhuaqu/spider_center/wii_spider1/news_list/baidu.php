<?php

/**
 * @filename baidu.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  17:22:20
 * @updatetime 2016-8-3  17:22:20
 * @version 1.0
 * @Description
 * 百度新闻搜索爬取中心
 * 
 */

// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/news_list/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use \SPIDER_CENTER\NEWS_LIST\Configure;

$spider_site = 'baidu';
$spider_name = $spider_site. '_'. Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND;

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site), LOG_LEVEL);

// 获取关键字
$task_response = get_keywords($spider_name, COLLECT_NAME);
//echo $task_response;
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

// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $key_word = $keyword['keyword'];
    // 翻页    
    $page = 1;
    while($page <= Configure::PAGE_NUM) {
        $spider_url = sprintf($url_host, urlencode('intitle:'. $key_word), ($page-1)*Configure::ONE_PAGE_NUM, Configure::ONE_PAGE_NUM);        // 构造url, 加上intitle:
        $page++;
        $spider_result = get_html($spider_url);     // 获取html
        $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

        $content_request['timestamp'] = time();
        $content_request['collect_url'] = $spider_url; 
        $content_request['keyword'] = $key_word;
        
        handle_one_page($spider_result, $spider_result_path, $content_request, $log);
        sleep(Configure::SLEEP_TIME);
    }
}
error:
    $log->INFO('爬取完毕');
	exit(0);
