<?php

/**
 * @filename cheshi_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-9  16:12:01
 * @updatetime 2016-8-9  16:12:01
 * @version 1.0
 * @Description
 * 车市api列表解析
 * 
 */

$collect_download_url = 'http://api.cheshi.com/services/mobile/api.php?api=mobile.wscs_v3.data&act=newslist&page=1&pagesize=200';
//$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/c2f8cf304c8dd017fcbb1ab058ef6b6b";

$json_str = file_get_contents($collect_download_url);
$json_array = json_decode($json_str, true);
$final_result = array();

$focus_list = $json_array['data']['focus'];
$data_list = $json_array['data']['data'];

foreach($focus_list as $focus) {
    var_dump($focus);
    if($focus['id'] == '') {
        continue;
    }
    
    $final_result[] = array(
        'id' => $focus['id'],
        'title' => $focus['title'],
        'url' => $focus['url'],
        'author'=>"",
        'source'=>1,
        'time' => $focus['story_date'],
        'media' => '网上车市手机客户端',
    );
}

foreach($data_list as $data) {
    
    if($data['id'] == '') {
        continue;
    }
    
    $final_result[] = array(
        'id' => $data['id'],
        'title' => $data['title'],
        'url' => $data['url'],
        'author'=>"",
        'source'=>1,
        'time' => $data['story_date'],
        'media' => '网上车市手机客户端',
        'collect_from_site' => $post_obj['collect_from_site'],
    );
}
print_r($final_result);
$response_result = $final_result;