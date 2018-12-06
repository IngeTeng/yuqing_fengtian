<?php

/**
 * @filename yiche_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-13  10:46:32
 * @updatetime 2016-8-13  10:46:32
 * @version 1.0
 * @Description
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/48d7e3b70007a1e77bc46279d8cbbf6a";

$content_url = 'http://api.app.yiche.com/webapi/news2014.ashx?newsid=%d&vshow=false';

$json_str = file_get_contents($collect_download_url);
$json_array = json_decode($json_str, true);
$final_result = array();

$data_list = $json_array['Data'];

foreach($data_list as $data) {
    
    if($data['newsid'] == '') {
        continue;
    }
    
    $final_result[] = array(
        'id' => $data['newsid'],
        'title' => $data['title'],
        'url' => sprintf($content_url, $data['newsid']),
        'time' => strtotime($data['publishtime']),
        'author' => $data['author'],
        'source' => $data['Source'],
        'media' => '手机易车网',
        'collect_from_site' => $post_obj['collect_from_site'],
        'summary' => $data['summary'],
    );
}

$response_result = $final_result;