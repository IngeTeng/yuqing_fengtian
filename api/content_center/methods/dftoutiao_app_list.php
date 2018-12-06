<?php

/**
 * @filename toutiao_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-14  22:17:58
 * @updatetime 2016-8-14  22:17:58
 * @version 1.0
 * @Description
 * 今日头条解析
 * 
 */

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/dftoutiao_app_log.txt'); //将出错信息输出到一个文本文件 


$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "E:/wamp64/www/yq/wii_spider/app_list/spider_result/f4b6d76965487ae89b4d1b4c7d36dec4";

$json_str = file_get_contents($collect_download_url);
$final_result = array();
//print_r($json_str);
if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);
    //print_r($json_array);
    $data_list = $json_array['data'];

    foreach($data_list as $data) {
        
        if(empty($data['url'])) {
            continue;
        }

        $final_result[] = array(
            'id'    => $data['urlpv'], 
            'title' => $data['topic'],
            'url' => $data['url'],
            'time' => strtotime($data['date']),
            'content' => $data['topic'],
            'author'=>$data['source'],
            'source'=>1,
            'media' => '东方头条',
            'from' => '东方头条',
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }    
}

$response_result = $final_result;
//print_r($final_result);