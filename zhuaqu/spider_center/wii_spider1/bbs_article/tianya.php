<?php

/**
 * @filename tianya.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  20:15:47
 * @updatetime 2016-8-3  20:15:47
 * @version 1.0
 * @Description
 * 天涯bbs抓取
 */


// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/bbs_article/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use SPIDER_CENTER\BBS_ARTICLE\Configure;

$spider_site = 'tianya';
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

// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $key_word = $keyword['keyword'];
    
    // 获取所有列表
    $list_url = sprintf($url_host, urlencode($key_word));        // 构造url
    $list_html = get_html($list_url);     // 获取html
    
    // 获取所有的文章列表
    $article_reg = '/<li>(.*?)<\/li>/s';
    preg_match_all($article_reg, $list_html, $article_results, PREG_SET_ORDER);     // 匹配所有列表,并排序

    $limit_time = time() - 86400;       // 一天内的文章
    $article_infos = array();

    foreach($article_results as $article) {
    	if(empty($article)) {
    		continue;
    	}
        $url_reg = '/<a href="(.*?)"/s';
        $time_reg = '/时间：<span>(.*?)<\/span>/s';

        preg_match($url_reg, $article[1], $url);
        preg_match($time_reg, $article[1], $time_str);
		if(empty($time_str)) {
    		continue;
    	}
		
        $time = strtotime($time_str[1]);
        if($time < $limit_time) {
            continue;
        }

        $infos['url'] = $url[1];
        $infos['time'] = $time;
        $infos["time_str"] = $time_str[1];
        $infos["limit_time"] = $limit_time;

        $article_infos[] = $infos;
    }

    if(empty($article_infos)) {
        $log->WARN("没有满足时间限制的爬取结果, keyword=$key_word");
        sleep(Configure::SLEEP_TIME);
        continue;       // 没有满足条件的爬取结果
    }

    foreach($article_infos as $article_info) {
        $spider_url = $article_info['url'];
        $spider_result = get_html($spider_url);
        $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

        $content_request['timestamp'] = time();
        $content_request['collect_url'] = $spider_url; 
        $content_request['keyword'] = $key_word;
        $content_request['bbs_public_time'] = $article_info['time'];
        
        handle_one_page($spider_result, $spider_result_path, $content_request, $log);
        sleep(Configure::SLEEP_TIME);
    }
}
error:
    $log->INFO('爬取完毕');
