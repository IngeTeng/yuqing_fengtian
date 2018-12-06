<?php

/**
 * @filename ifeng.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-17  14:00:16
 * @updatetime 2016-8-17  14:00:16
 * @version 1.0
 * @Description
 * 凤凰网汽车论坛抓取
 * 
 */

// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/bbs_article/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use SPIDER_CENTER\BBS_ARTICLE\Configure;

$spider_site = 'ifeng';
$spider_name = $spider_site. '_'. Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND;

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site), LOG_LEVEL);

// 初始内容中心化请求
$content_request = array(
    "timestamp" => '',
    "collect_name" => COLLECT_NAME,
    "collect_ip" => COLLECT_HOST,
    "collect_kind" => 'forum',
    "collect_from_site" => $spider_site,
    "collect_content_kind" => 'list',
    "collect_download_url" => "",
    "collect_url" => "",
    "keyword" => "",
    "spider_kind" => Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND,
);

$url_host = Configure::$url_hosts[$spider_site];

// 解析论坛列表
$content_request['timestamp'] = time();
$content_request['collect_download_url'] = $url_host;
$content_request['sign'] = Post2Sign::getSign($content_request, SECRET);       // 进行加密
$forum_response = send_post(CONTENT_CENTER, $content_request);
echo $forum_response;
$forum_array = json_decode($forum_response, true);
if($forum_array['status'] != REQUEST_OK) {
    $log->WARN("凤凰论坛列表解析出错");
    goto error;
}
$forum_list = $forum_array['result'];
$log->INFO('凤凰论坛列表解析成功');
// 获取文章列表
$content_request['collect_kind'] = 'bbs';       // 解析文章列表
$article_infos = array();
foreach($forum_list as $forum) {
    $content_request['timestamp'] = time();
    $content_request['collect_download_url'] = $forum;
    unset($content_request['sign']);
    $content_request['sign'] = Post2Sign::getSign($content_request, SECRET);
    $article_response = send_post(CONTENT_CENTER, $content_request);
    $article_array = json_decode($article_response, true);
    if($article_array['status'] != REQUEST_OK and $article_array['status'] != 501) {
        $log->WARN("凤凰论坛文章列表解析出错 forum_url:$forum");
        goto error;
    }
    
    foreach($article_array['result'] as $article_info) {
        $article_infos[] = $article_info;
    }
}
$log->INFO('凤凰论坛文章列表解析成功');

// 解析文章内容
foreach($article_infos as $article_info) {
    $spider_url = $article_info['url'];
    $spider_result = get_html($spider_url);
    
    // 如果触发反爬虫 直接保存已经解析的结果到ifeng_result中
    if(strpos($spider_result, '<div class="t_fsz">') === false) {
        $log->WARN('触发凤凰汽车论坛的反爬虫');
        // 保存解析结果
        $ifeng_result_path = Configure::get_ifeng_result_path(BASE_PATH);
        if (is_dir($ifeng_result_path)) {
            $temp_result = json_encode($article_info);
            $result_name = hash("md5", $temp_result). '.json';
            $spider_file_result = save_file($ifeng_result_path, $result_name, $temp_result);
            
            if($spider_file_result === false) {
                $log->WARN("析结果保存失败");
                continue;     // 结束爬虫
            }
            else {
                $log->INFO("解析结果保存成功, FILENAME=". $ifeng_result_path. "/$result_name");
            }
        }
    }
    else {      // 没有触发反爬虫,正常处理
        $request['timestamp'] = time();
        $request['collect_url'] = $spider_url; 
        $request['collect_content_kind'] = 'content';       // 改变内容类型, 解析内容

        $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

        $content_request['timestamp'] = time();
        $content_request['collect_url'] = $spider_url; 
        $content_request['keyword'] = '';

        handle_one_page($spider_result, $spider_result_path, $content_request, $log);
    }
}
error:
    $log->INFO('爬取完毕');
