<?php

/**
 * @filename ifeng_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-13  12:06:39
 * @updatetime 2016-8-13  12:06:39
 * @version 1.0
 * @Description
 * 凤凰网解析
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/d08df77d72e63bd6b5248d6a230a7a10";

$content_url = 'http://api.3g.ifeng.com/ipadtestdoc?aid=%s';

$json_str = file_get_contents($collect_download_url);
$json_array = json_decode($json_str, true);
$final_result = array();
if(isset($json_array[0]['item'])) {
    $data_list = $json_array[0]['item'];

    foreach($data_list as $data) {

        if($data['documentId'] == '' or !isset($data['updateTime'])) {
            continue;
        }

        $final_result[] = array(
            'id' => $data['documentId'],
            'title' => $data['title'],
            'url' => sprintf($content_url, $data['documentId']),
            'author' => $data['source'],
            'source' => 1,
            'time' => strtotime($data['updateTime']),
            'media' => '凤凰新闻客户端',
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }
}
$response_result = $final_result;