<?php

/**
 * @filename toutiao_appsearch_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-15  21:34:39
 * @updatetime 2016-11-15  21:34:39
 * @version 1.0
 * @Description
 * 今日头条appsearch解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing2/wii_spider/appsearch_list/spider_result/2d0fefb701c5a8ec654a3214f3b52d2b";

$json_str = file_get_contents($collect_download_url);
$final_result = array();
if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);
    $data_list = $json_array['data'];
    
    foreach($data_list as $data) {
        if(empty($data['title']) || empty($data['article_url']) || empty($data['publish_time']) || empty($data['abstract'])) {
            continue;
        }
        
        $final_result[] = array(
            'title' => $data['title'],
            'url' => $data['article_url'],
            'time' => $data['publish_time'],
            'content' => $data['abstract'],
            'author'=>$data['source'],
            'source'=>1,
            'channel' => empty($data['media_name'])?'':$data['media_name'],
            'from' => '今日头条',
        );
    }    
}
//print_r($final_result);
$response_result = $final_result;
