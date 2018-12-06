<?php
// 引入配置文件和一些工具类 常用函数
require('../config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/weixin_list/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use SPIDER_CENTER\WEIXIN_LIST\Configure;

$spider_site = 'sogou';
$spider_name = $spider_site. '_'. Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND;

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site.'_bu'), LOG_LEVEL);

$key_word = $_POST['kids'];
//print_r($key_word[0]);
$spider_result = $_POST['code'];
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

$spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径

$file_url = '手动补入';
//$key_word = '凯美瑞';
$content_request['timestamp'] = time();
$content_request['collect_url'] = $file_url; 
$content_request['keyword'] = $key_word[0];
//$spider_result = file_get_contents($file_url);
//$spider_result = iconv("gb2312", "utf-8//IGNORE",$spider_result);
handle_one_page($spider_result, $spider_result_path, $content_request, $log);
echo 'Submit success! The system needs some minutes to deal with these codes, please wait...And you can close this window and continue your work.';
?>