<?php

/**
 * @filename zaker_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  16:37:32
 * @updatetime 2016-8-15  16:37:32
 * @version 1.0
 * @Description
 * zaker新闻客户端api解析
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/fd5bfe26cd5701c6b3a263410f589023";

$json_str = file_get_contents($collect_download_url);
$final_result = array();
if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);
    $data_list = $json_array['data']['articles'];
    
    foreach($data_list as $data) {
        
        if(empty($data['date']) or empty($data['full_url'])) {
            continue;
        }
        
        $final_result[] = array(
            'id' => $data['pk'],
            'title' => $data['title'],
            'url' => $data['full_url'],
            'time' => strtotime($data['date']),
            'channel' => $data['auther_name'],
            'media' => 'ZAKER',
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }    
}

$response_result = $final_result;
