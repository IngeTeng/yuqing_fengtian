<?php

/**
 * @filename spider_center1.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-20  12:03:17
 * @updatetime 2016-8-20  12:03:17
 * @version 1.0
 * @Description
 * 爬虫中心
 * 负责调度各个小爬虫
 * 
 */

require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/include/log.php');

// 任务中心请求初始化
$task_request = array(
    'collect_name' => COLLECT_NAME,
    'spider_name' => '',
    'timestamp' => '',
    'request' => 1,     // 请求关键字
);

$SPIDER_SLEEP = json_decode(SPIDER_SLEEP, true);
asort($SPIDER_SLEEP);
$last_spider = array();     // 用于记录各个爬虫的上次爬取时间
$min_sleep = current($SPIDER_SLEEP);        // 找到最小的时间,用于总体sleep

while(true) {
	$log_handler = LOG_HANDLER. date('Y_m_d', time());
	$log = Log::Init($log_handler, LOG_LEVEL);
    foreach($SPIDER_SLEEP as $key => $value) {
    
        $spider_info = explode('_', $key, 2);       // 解析爬虫名
        $spider_kind = $spider_info[1];
        $spider_site = $spider_info[0];
        $spider_path = BASE_PATH. "/$spider_kind". "/$spider_site.php";       // 拼接爬虫目录
            
        if(!isset($last_spider[$key])) {        // 如果是第一次爬取
            if(is_file($spider_path)) {
                
                $last_spider[$key] = time();        // 记录爬取时间
                $log->INFO("找到爬虫 $key");
                system("nohup ". PHP_PATH. " $spider_path >>output &");
            }
            else {
                $log->WARN("找不到爬虫 $key, path=$spider_path");
                unset($SPIDER_SLEEP[$key]);
                continue;
            }
        }
        else {
        	if((time() - $last_spider[$key] >= $value) and is_file($spider_path)) {
                $log->INFO("找到爬虫 $key, 上次爬取时间为". date('Y-m-d H:i:s', $last_spider[$key]));
                $last_spider[$key] = time();        // 记录爬取时间
                system("nohup ". PHP_PATH. " $spider_path >>output &");
            }
            else {
                continue;
            }
        }
    }
    //break;
    unset($log);
    unset($log_handler);
    sleep(100);
}