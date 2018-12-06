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
require(BASE_PATH. "/bbs_list/config.php");
require(BASE_PATH. '/include/log.php');
ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/autohome-all_log.txt'); //将出错信息输出到一个文本文件 

// 调用命名空间
use SPIDER_CENTER\BBS_LIST\Configure;

$spider_site = 'autohome-all';
$spider_name = $spider_site. '_'. Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND;

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site), LOG_LEVEL);

// 获取关键字
$task_response = get_keywords($spider_name, COLLECT_NAME);  
$task_array = json_decode($task_response, true);
if($task_array['status'] != REQUEST_OK) {
    $log->WARN("{$task_array['status']}  获取关键字失败, spider=$spider_name");
    goto error;
}

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

$url_add  = array(
    '凯美瑞' => '110', //1000
    '汉兰达' => '771', //1000
    '埃尔法' => '2107', //69
    '致炫'   => '3126', //791
    '雷凌'   => '3462', //1000
    '致享'   => '4259', //94
    'C-HR'  =>  '4645', //37
    'IX4'   =>  '4793'   //1页
    );
$url_page  = array(
    '凯美瑞' => 5, //1000
    '汉兰达' => 5, //1000
    '埃尔法' => 2, //69
    '致炫'   => 3, //791
    '雷凌'   => 5, //1000
    '致享'   => 2, //94
    'C-HR'  =>  1, //37
    'IX4'   =>  1   //1页
    );

// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $key_word = $keyword['keyword'];
    if( !isset($url_add[ $key_word]) ){
        continue;
    }
    // 翻页    
    $page = 1;
    while($page <= $url_page[ $key_word ]) {
        $spider_url = sprintf($url_host, $url_add[ $key_word ], $page);      // 构造url
        $page++;
        foreach($proxy_infos as $proxy_info) {
            unset($spider_result);
            $begin_time = time();
            // $ctx = stream_context_create(array( 
            //         'http' => array(
            //                 'timeout' => 30, 
            //                 'proxy' => 'tcp://'.$proxy_info['proxy'].':'.$proxy_info['port'], 
            //                 'request_fulluri' => True,
            //             ) 
            //         ) 
            //     ); 
            //$spider_result = file_get_contents($spider_url, False, $ctx); 
            $cookie = 'CYHTooltip=1; fvlid=15102029054748Lmh25Qn; sessionid=247CC699-EDAB-4A82-9035-2C67219AD938%7C%7C2017-11-09+12%3A48%3A22.416%7C%7C121.40.40.203; Hm_lvt_9924a05a5a75caf05dbbfb51af638b07=1510202907,1510202912,1510202921; cn_1262640694_dplus=%7B%22distinct_id%22%3A%20%2215f9f1b95a952e-0d04b6b717be7b-6b1b1279-15f900-15f9f1b95aa64d%22%2C%22sp%22%3A%20%7B%22%24_sessionid%22%3A%200%2C%22%24_sessionTime%22%3A%201510202926%2C%22%24dp%22%3A%200%2C%22%24_sessionPVTime%22%3A%201510202926%7D%7D; UM_distinctid=15f9f1b95a952e-0d04b6b717be7b-6b1b1279-15f900-15f9f1b95aa64d; historybbsName4=c-4259%7CYARiS%20L%20%E8%87%B4%E4%BA%AB%2Cc-110%7C%E5%87%AF%E7%BE%8E%E7%91%9E%2Cc-3462%7C%E9%9B%B7%E5%87%8C; sessionuid=247CC699-EDAB-4A82-9035-2C67219AD938%7C%7C2017-11-09+12%3A48%3A22.416%7C%7C121.40.40.203; __ah_uuid=B1FE9317-3138-403C-877C-6D33CF250F95; __guid=37235314.1830710374340494600.1539244911993.8518; sessionip=124.115.222.150; sessionvid=187B3E30-1CEC-4081-9847-AB5F0AFA2084; area=610113; ahpau=1; __utma=1.378078923.1539244935.1539244935.1539244935.1; __utmb=1.0.10.1539244935; __utmc=1; __utmz=1.1539244935.1.1.utmcsr=club.autohome.com.cn|utmccn=(referral)|utmcmd=referral|utmcct=/bbs/jx-c-110-1.html; autoac=C444DCAEADC5FFFEAB2AFF023CE89ADD; autotc=73A2E9028B483BA2C256BF17C75B6C5A; monitor_count=9; ahpvno=11; pvidchain=3311253,101061,101061,101061,101061,101061; ref=www.baidu.com%7C0%7C0%7Chao.360.cn%7C2018-10-11+16%3A04%3A21.330%7C2018-07-13+18%3A21%3A28.324; ahrlid=1539245056588qgn1Fy1E-1539245182373';
            //$spider_result = file_get_contents($spider_url);     // 获取html源码
            $spider_result = get_html($spider_url, $cookie, $proxy_info['proxy'], $proxy_info['port']);
            if(empty($spider_result) or strstr($spider_result, '403 Forbidden') != false or strpos($spider_result, 'Error 404') != false or strstr($spider_result, 'account/safety') != false or strstr($spider_result, 'autohome') === false or strstr($spider_result, 'Not Found') != false) {
                $log->WARN("无效代理, {$proxy_info['proxy']}:{$proxy_info['port']}");
                $useless_proxy[] = $p_id-1;        // 记录无效代理, 由于前15个关键字是本地抓取,要-1
                unset($proxy_infos[$p_id]);      // 删除无效代理
                $p_id++;
            }else {
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
        //exit();
        sleep(Configure::SLEEP_TIME);
    }
    
    
}
error:
    $log->INFO('爬取完毕');
    exit();
