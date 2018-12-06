<?php

/**
 * @filename ku6.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-16  16:05:43
 * @updatetime 2016-8-16  16:05:43
 * @version 1.0
 * @Description
 * ku6视频抓取
 */

// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/video_list/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use SPIDER_CENTER\VIDEO_LIST\Configure;

$spider_site = 'ku6';
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

$cookie = 'KUID=1481704806773986; PHPSESSID=9iqm4jaua9is37apa5qiqtiu22; o_b_t_s=855.30.23.122.281704836324.6; vids=5efvKXQ8qL-TrzAjIOt-1w..; RateInfo={"RateInfo":"799@\u9ad8  \u6e05|450@\u6807  \u6e05|799@\u9ad8  \u6e05|1500@\u8d85  \u6e05"}; Gone=223001; ServerIP=120.203.214.106; swf_video_ip=120.203.214.106; AheadTime=1800; Location=http://120.203.214.106/548/62/95/d0c10d780bd1afee0f8cf22627230025-f4v-h264-aac-638-32-214042.0-18100561-1478445351298-0d760bc417febb9b27f2447b0ead8b32-1-00-00-00.f4v?vid=123819507&lp=8081&lroot=/0&kfd=1&cmd=0&uuid=DFB2404D1C30D18B18037F642EF6E1A013EDAF3C&srchost=183.131.106.167&srcroot=/1&s=0&tm=1481792680&key=5cd69c7ee63e54aa29893a938e226d6e&diskid=21&lr=0&nlh=0&check=1&id=ku6_vod&usrip=222.41.148.254&uloc=27.0.3&ipsm=1&ext=.f4v; Hm_lvt_fcc6287f147d80d0e8d5c7655a907737=1481706289,1481706294,1481706349,1481706362; Hm_lpvt_fcc6287f147d80d0e8d5c7655a907737=1481707016';

// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $key_word = $keyword['keyword'];
    $spider_url = sprintf($url_host, urlencode($key_word));     // 构造url
    $spider_result = get_html($spider_url,$cookie);     // 获取html
    $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

    $content_request['timestamp'] = time();
    $content_request['collect_url'] = $spider_url; 
    $content_request['keyword'] = $key_word;
    
    handle_one_page($spider_result, $spider_result_path, $content_request, $log);
    sleep(Configure::SLEEP_TIME);
}
error:
    $log->INFO('爬取完毕');
