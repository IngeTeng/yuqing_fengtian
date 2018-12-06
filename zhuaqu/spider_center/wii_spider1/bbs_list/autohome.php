<?php

/**
 * @filename autohome.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  21:34:06
 * @updatetime 2016-8-15  21:34:06
 * @version 1.0
 * @Description
 * 汽车之家论坛
 * 
 */

// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/bbs_list/config.php');
require(BASE_PATH. '/include/log.php');

// ini_set('display_errors',1);            //错误信息  
// ini_set('display_startup_errors',1);    //php启动错误信息  
// error_reporting(-1);                    //打印出所有的 错误信息  
// ini_set('error_log', dirname(__FILE__) . '/autohome_Log.txt'); //将出错信息输出到一个文本文件 

// 调用命名空间
use SPIDER_CENTER\BBS_LIST\Configure;

$spider_site = 'autohome';
$spider_name = $spider_site. '_'. Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND;

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site), LOG_LEVEL);

// 获取关键字
$i=5;
while($i--){//有时候获取关键字会失败，多试几次
    $task_response = get_keywords($spider_name, COLLECT_NAME);  
    $task_array = json_decode($task_response, true);
    if($task_array['status'] == REQUEST_OK){//成功则跳出循环
        break;
    }
    sleep(10);//休息10秒
}

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

$proxy_infos = array_merge(array(array('proxy' => '', 'port' => '')), $task_array['proxy']);
$useless_proxy = array('empty');

$k_id = 1;
$p_id = 0;

$type = array('luntan', 'zhidao');
// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $k_id++;
    $key_word = $keyword['keyword'];

    foreach ($type as $key => $t) {//先爬论坛数据，再爬问答数据
        $spider_url = sprintf($url_host, $t, urlencode(iconv('utf-8', 'gbk', $key_word)));    // 构造url
        //$spider_result = get_html($spider_url);     // 获取html

        foreach($proxy_infos as $proxy_info) {
            unset($spider_result);
            $begin_time = time();
            $ctx = stream_context_create(array( 
                    'http' => array(
                            'timeout' => 30, 
                            'proxy' => 'tcp://'.$proxy_info['proxy'].':'.$proxy_info['port'], 
                            'request_fulluri' => True,
                        ) 
                    ) 
                ); 
            //$spider_result = file_get_contents($spider_url, False, $ctx); 
            $spider_result = get_html($spider_url, '', $proxy_info['proxy'], $proxy_info['port']);
            //file_put_contents('html-autohome', $spider_result);
            if(empty($spider_result) or strpos($spider_result, '403 Forbidden') != false or strpos($spider_result, 'Error 404') != false) {
                $log->WARN("无效代理, {$proxy_info['proxy']}:{$proxy_info['port']}");
                $useless_proxy[] = $p_id-1;        // 记录无效代理, 由于前15个关键字是本地抓取,要-1
                unset($proxy_infos[$p_id]);      // 删除无效代理
                $p_id++;
            }
            else {
                if(!empty($proxy_info['proxy'])) {
                    $log->INFO("成功使用代理爬取, {$proxy_info['proxy']}:{$proxy_info['port']}");
                }
                if(time() - $begin_time > 40) {     // 去除响应过慢的代理
                    unset($proxy_infos[$p_id]);
                    $p_id++;
                }
                break;
            }
        }
        if(empty($spider_result)) {     // 如果所有代理都用完,直接结束爬虫
            goto error;
        }

        $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径
        
        $content_request['timestamp'] = time();
        $content_request['collect_url'] = $spider_url; 
        $content_request['keyword'] = $key_word;
        
        handle_one_page($spider_result, $spider_result_path, $content_request, $log);

        if($k_id > 15 and empty($proxy_info['proxy'])) {        // 本地服务器爬取15个关键字就换代理
            unset($proxy_infos[$p_id]);
            $p_id++;
        }
        sleep(Configure::SLEEP_TIME);
    }

    
}
error:
 
    $log->INFO('爬取完毕');
    exit(0);
