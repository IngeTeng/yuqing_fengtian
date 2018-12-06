<?php

/**
 * @filename toutiao.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-15  21:18:29
 * @updatetime 2016-11-15  21:18:29
 * @version 1.0
 * @Description
 * 今日头条appsearch
 * 
 */

// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/appsearch_list/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use \SPIDER_CENTER\APPSEARCH_LIST\Configure;

$spider_site = 'toutiao';
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

$proxy_infos = array_merge(array(array('proxy' => '', 'port' => '')), $task_array['proxy']);
$useless_proxy = array('empty');

$k_id = 1;
$p_id = 0;

// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $k_id++;
    $key_word = $keyword['keyword'];

    $spider_url = sprintf($url_host, urlencode($key_word));        // 构造url
    foreach($proxy_infos as $proxy_info) {
        unset($spider_result);
        $begin_time = time();
        $spider_result = get_html($spider_url, '', $proxy_info['proxy'], $proxy_info['port']);
        if(empty($spider_result) or false != strpos($spider_result, 'error')  or false != strpos($spider_result, '502 Bad Gateway')) {
            $log->WARN("无效代理, {$proxy_info['proxy']}:{$proxy_info['port']}");
            $useless_proxy[] = $p_id-1;        // 记录无效代理, 由于前15个关键字是本地抓取,要-1
            unset($proxy_infos[$p_id]);      // 删除无效代理
            $p_id++;
        }
        elseif(false !== strpos($spider_result, 'code_tit')) {
            $log->WARN("触发反爬虫, {$proxy_info['proxy']}:{$proxy_info['port']}");
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
error:
    // 删除无效proxy
    // $proxy_request = array('useless_proxy' => $useless_proxy, 'spider_name' => 'toutiao_appsearch_list');
    // unset($proxy_request['sign']);
    // $proxy_request['sign'] = Post2Sign::getSign($proxy_request, SECRET);
    // send_post(TASK_CENTER, $proxy_request);   
    $log->INFO('爬取完毕');


// 获取的html,带模拟登陆
function get_html2($url, $cookie='', $proxy='', $proxy_port='', $referer='') {
    $ch = curl_init();
    // 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);      // 60秒超时

    if($cookie != '') {
        $coo = "Cookie:$cookie";
        $headers[] = $coo;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($referer != '') {
        curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
    if($proxy != '' and $proxy_port != '') {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    
    // 获取内容
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
