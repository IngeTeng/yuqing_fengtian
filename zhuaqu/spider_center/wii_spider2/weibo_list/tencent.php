<?php

/**
 * @filename tencent.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  19:25:06
 * @updatetime 2016-8-3  19:25:06
 * @version 1.0
 * @Description
 * 
 */

// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/weibo_list/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use SPIDER_CENTER\WEIBO_LIST\Configure;

$spider_site = 'tencent';
$spider_name = $spider_site. '_'. Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND;

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site), LOG_LEVEL);

// 获取关键字

$useless_cookies = array('empty');
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

// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $key_word = $keyword['keyword'];
    $spider_url = sprintf($url_host, urlencode($key_word));     // 构造url
    
    // 使用cookie
    $cookies = $task_array['cookies'];
    $cookies_num = count($cookies);
    for($c_i = 0; $c_i < $cookies_num; $c_i++) {
        $spider_result = get_html($spider_url, $cookies[$c_i]['cookie']);
        if(strpos($spider_result, $key_word)) {
           break; 
        }
        else {
            $log->WARN('cookies失效, c_id = '. $cookies[$c_i]['id']);
            $useless_cookies[] = $cookies[$c_i]['id'];      // 记录失效cookies
            unset($cookies[$c_i]);      // 删除失效cookies
        }
    }
    if($cookies_num === (count($useless_cookies)-1)) {
        $log->WARN('所有cookies都失效!');
        goto error;     // 所有cookies失效,结束爬虫
    } 

    $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

    $content_request['timestamp'] = time();
    $content_request['collect_url'] = $spider_url; 
    $content_request['keyword'] = $key_word;
    
    handle_one_page($spider_result, $spider_result_path, $content_request, $log);
    sleep(Configure::SLEEP_TIME);
}
error:
    // 删除无效cookies
    $cookies_request = array('useless_cookies' => $useless_cookies, 'spider_name' => 'tencent_weibo_list');
    unset($cookies_request['sign']);
    //$post2sign = new Post2Sign($cookies_request, SECRET);
    $cookies_request['sign'] = Post2Sign::getSign($proxy_request, SECRET);
    send_post(TASK_CENTER, $cookies_request);      
    $log->INFO('爬取完毕');
    