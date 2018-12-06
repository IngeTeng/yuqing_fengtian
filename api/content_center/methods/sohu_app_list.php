<?php

/**
 * @filename sohu_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-13  17:29:59
 * @updatetime 2016-8-13  17:29:59
 * @version 1.0
 * @Description
 * 搜狐app列别解析
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/9549132ae936d3f84a7f63e7a0f340d4";

$content_url = 'http://zcache.k.sohu.com/api/news/cdn/v1/article.go/%d/1/603/0/3/1/18/25/3/1/1/53324600.xml';

$json_str = file_get_contents($collect_download_url);
$final_result = array();

if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);

    $data_list = $json_array['articles'];

    foreach($data_list as $data) {

        if(!isset($data['updateTime'])) {
            continue;
        }
        $updatetime = substr($data['updateTime'], 0, 10);
        $final_result[] = array(
            'id' => $data['newsId'],
            'title' => $data['title'],
            'url' => sprintf($content_url, $data['newsId']),
            'author' => $data['media'],
            'source' => $data['isPreload'],
            'time' => $updatetime,
            'media' => '搜狐新闻客户端',
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }    
}

$response_result = $final_result;