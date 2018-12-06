<?php

/**
 * @filename xcar_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  16:00:44
 * @updatetime 2016-8-15  16:00:44
 * @version 1.0
 * @Description
 * 爱卡汽车api解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/3304c13cc3f7f801c840ec27439ae5ee";

$json_str = file_get_contents($collect_download_url);
$final_result = array();
if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);
    $data_list = $json_array['news'];

    foreach($data_list as $data) {
        
        $final_result[] = array(
            'id' => $data['newsId'],
            'title' => $data['newsTitle'],
            'url' => $data['newsLink'],
            'time' => $data['createDate'],
            'author'=>"",
            'source'=>1,
            'media' => '爱卡汽车客户端',
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }    
}

$response_result = $final_result;

