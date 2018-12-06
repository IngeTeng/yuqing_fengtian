<?php

/**
 * @filename autohome_dealer.php 
 * @encoding UTF-8 
 * @author WiiPu YJL 
 * @createtime 2018-09-24  20:00:00
 * @updatetime 
 * @version 1.0
 * @Description
 * 抓取汽车之家经销商频道
 *
 */

// 引入配置文件和一些工具类 常用函数
require('../config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. "/news_list/config.php");
require(BASE_PATH. '/include/log.php');
ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/autohome-dealer_log.txt'); //将出错信息输出到一个文本文件 

// 调用命名空间
use \SPIDER_CENTER\NEWS_LIST\Configure;

$spider_site = 'autohome-dealer';
$spider_name = $spider_site. '_'. Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND;

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site), LOG_LEVEL);

// 获取关键字
$task_response = get_keywords($spider_name, COLLECT_NAME);  
$task_array = json_decode($task_response, true);
if($task_array['status'] != REQUEST_OK) {
    $log->WARN("{$task_array['status']}  获取关键字失败, spider=$spider_name");
    goto error;
}
// for($i=1; $i < 31; $i++){

//     $url = 'http://dealer.autohome.com.cn/china/0/3/0/155/'.$i.'/1/0/0.html';
//     $result = file_get_contents($url);
//     file_put_contents('html', $result);
//     $dealers_reg = '/<a class="img-box" href="\/\/dealer.autohome.com.cn\/(.*?)\/#pvareaid=/s';
//     preg_match_all($dealers_reg, $result, $dealers_result, PREG_SET_ORDER);
//     file_put_contents('dealers', json_encode($dealers_result), FILE_APPEND);

// }
$url2 = 'dealers';
$result = file_get_contents($url2);
$dealers_arr = json_decode($result, true);
//file_put_contents('dealers2', var_export($dealers_arr, true));
// exit();
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
$url_add  = array(
    '凯美瑞' => 'newslist_c0_s110.html#title', 
    '汉兰达' => 'newslist_c0_s771.html#title',
    '埃尔法' => 'newslist_c0_s2107.html#title',
    '致炫'   => 'newslist_c0_s3126.html#title',
    '雷凌'   => 'newslist_c0_s3462.html#title',
    '致享'   => 'newslist_c0_s4259.html#title',
    'C-HR'  =>  'newslist_c0_s4645.html#title',
    'IX4'   =>  'newslist_c0_s4793.html#title'
    );

// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $key_word = $keyword['keyword'];
    foreach ($dealers_arr as $dealer)//遍历全国所有的代理商
    {
        if( !isset($url_add[ $key_word]) ){
            break;
        }
        $spider_url = sprintf($url_host, $dealer['1'], $url_add[ $key_word ]);      // 构造url
        
        $spider_result = file_get_contents($spider_url);     // 获取html源码

        $dealer_url      = 'http://dealer.autohome.com.cn/Ajax/GetDealerInfo?DealerId='.$dealer[1];
        $spider_result  .= get_html($dealer_url);
        $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径       
        $content_request['timestamp'] = time();
        $content_request['collect_url'] = $spider_url.'#&#'.$dealer_url; 
        $content_request['keyword'] = $key_word;
        
        handle_one_page($spider_result, $spider_result_path, $content_request, $log);
        //exit();
        sleep(Configure::SLEEP_TIME);
        
    }
}
error:
    $log->INFO('爬取完毕');
