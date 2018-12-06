<?php

/**
 * @filename sougou.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  20:04:00
 * @updatetime 2016-8-3  20:04:00
 * @version 1.0
 * @Description
 * 
 */

// 引入配置文件和一些工具类 常用函数
require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/weixin_list/config.php');
require(BASE_PATH. '/include/log.php');

// 调用命名空间
use SPIDER_CENTER\WEIXIN_LIST\Configure;

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/weixin_log.txt'); //将出错信息输出到一个文本文件 

$spider_site = 'sogou';
$spider_name = $spider_site. '_'. Configure::COLLECT_KIND. '_'. Configure::COLLECT_CONTENT_KIND;

$log = Log::Init(Configure::getLogHandle(BASE_PATH, $spider_site), LOG_LEVEL);

// 获取关键字
$useless_cookies = array('empty');

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
$proxy_infos = array_merge(array(array('proxy' => '', 'port' => '')), $task_array['proxy']);//获取代理
//file_put_contents('daili', var_export($proxy_infos,TRUE));
$useless_proxy = array('empty');
$cookies = $task_array['cookies'];//获取cookie
$cookies_num = count($cookies);
$k_id = 1;
//$p_id = 0;

// 遍历关键字    
foreach($task_array['keywords'] as $keyword) {
    $k_id++;
    $key_word = $keyword['keyword'];
    $page = 1;
    // 翻页

    while($page <= Configure::PAGE_NUM) {
        $spider_url = sprintf($url_host, urlencode($key_word), $page);      // 构造url
        $page++;
        
        
        // 使用cookie
        /**
        for($c_i = 0; $c_i < $cookies_num; $c_i++) {
        	if(empty($cookies[$c_i])) {
        		continue;
        	}
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
        */
            unset($spider_result);
            $flag = 0;//用于跳出cookie循环
            while(true) {//使用cookie
                if($cookies_num === (count($useless_cookies)-1)) {
                    $log->WARN('所有cookies都失效!');
                    goto error;     // 所有cookies失效,结束爬虫
                }
                
                $c_i = rand(0, $cookies_num-1);
                if(empty($cookies[$c_i])) {
                    continue;
                }
                foreach($proxy_infos as $proxy_info) {//使用代理
                    $header = get_headers($spider_url);
                    //$spider_result = file_get_contents($spider_url);
                    //file_put_contents('weixin-headers', $header);
                    $spider_result = get_html($spider_url.'&tsn=1', $cookies[$c_i]['cookie'], $proxy_info['proxy'], $proxy_info['port'], $spider_url);//使用referer
                    
                    if(strpos($spider_result, $key_word) and strpos($spider_result, '访问出错') == false) {
                    	$log->INFO("抓取成功， cookie=". $cookies[$c_i]['id'].'daili:'.$proxy_info['proxy'].':'.$proxy_info['port']);
                        file_put_contents('weixin-html-right', $spider_result);
                       $flag = 1;
                       break; 
                    }else {
                        $log->INFO("抓取失败， cookie=". $cookies[$c_i]['id'].'daili:'.$proxy_info['proxy'].':'.$proxy_info['port']);
                        file_put_contents('weixin-html-wrong', $spider_result);
                    }
                   // $p_id ++;//尝试下一个代理
                    
                }
                if($flag == 1){
                    break;
                }else {//代理用完了都没抓取成功，说明该cookie失效
                    $log->WARN('cookies失效, c_id = '. $cookies[$c_i]['id']);
                   // file_put_contents('html', $spider_result);
                    $useless_cookies[] = $cookies[$c_i]['id'];      // 记录失效cookies
                    unset($cookies[$c_i]);      // 删除失效cookies
                    
                }
            }
            
        
        $spider_result_path = Configure::get_spider_result_path(BASE_PATH);     // 获取保存路径
        
        $content_request['timestamp'] = time();
        $content_request['collect_url'] = $spider_url; 
        $content_request['keyword'] = $key_word;
        
        handle_one_page($spider_result, $spider_result_path, $content_request, $log);


        sleep(rand(Configure::SLEEP_TIME-10, Configure::SLEEP_TIME+10));
    }
    
    if($key_word == '雅阁' or $key_word == '迈锐宝') {
    	$page = 1;
        sleep(rand(Configure::SLEEP_TIME-10, Configure::SLEEP_TIME+10));
        while($page <= Configure::PAGE_NUM) {
        	$spider_url = sprintf($url_host, urlencode($key_word. ' 混动'), $page);      // 构造url
        	$log->INFO("混动！！！");
        	$page++;
        
        	// 使用cookie
        	$useless_cookies = array('empty');
        	for($c_i = 0; $c_i < $cookies_num; $c_i++) {
        		if(empty($cookies[$c_i])) {
        			continue;
        		}	
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
        	sleep(rand(Configure::SLEEP_TIME-10, Configure::SLEEP_TIME+10));
    	}	  	
    }
    if(!empty($useless_cookies)){//实现及时更新Cookie状态
        // 删除无效cookies
        $cookies_request = array('useless_cookies' => $useless_cookies, 'spider_name' => 'sogou_weixin_list');
        unset($cookies_request['sign']);
        $post2sign = Post2Sign::getSign($cookies_request, SECRET);
        $cookies_request['sign'] = $post2sign;
        send_post(TASK_CENTER, $cookies_request); 
        $log->INFO('已发送删除Cookie请求');
        $useless_cookies = array();
    }

}
error:
    // 删除无效cookies
    // $cookies_request = array('useless_cookies' => $useless_cookies, 'spider_name' => 'sogou_weixin_list');
    // unset($cookies_request['sign']);
    // $post2sign = Post2Sign::getSign($cookies_request, SECRET);
    // $cookies_request['sign'] = $post2sign;
    // send_post(TASK_CENTER, $cookies_request); 
    $log->INFO('爬取完毕');
    exit();


