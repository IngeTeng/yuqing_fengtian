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


$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/89912fe777869df01c744ab1465bc4a2";

$json_str = file_get_contents($collect_download_url);
$final_result = array();

if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);

    $data_list = $json_array['data'];

    foreach($data_list as $data) {
        
        if(empty($data['abstract'])) {
            continue;
        }

        $final_result[] = array(
            'id' => $data['tag_id'],
            'title' => $data['title'],
            'url' => $data['share_url'],
            'author' => $data['source'],
            'source' => 1,
            'time' => $data['publish_time'],
            'content' => $data['abstract'],
            'media' => '今日头条',
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }    
}

$response_result = $final_result;