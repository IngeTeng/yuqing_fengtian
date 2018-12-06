<?php

/**
 * @filename 163_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-14  21:04:11
 * @updatetime 2016-8-14  21:04:11
 * @version 1.0
 * @Description
 * 网易api解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/ccef22a80be2be6cc930f7d5bc216654";

$list_id = $post_obj['list_id'];
$json_str = file_get_contents($collect_download_url);
$json_array = json_decode($json_str, true);
$final_result = array();
if(isset($json_array[$list_id])) {
    $data_list = $json_array[$list_id];

    foreach($data_list as $data) {

        if(!isset($data['url'])) {
            continue;
        }

        $final_result[] = array(
            'id' => $data['docid'],
            'title' => $data['title'],
            'url' => $data['url'],
            'time' => strtotime($data['ptime']),
            'author'=>$data['source'],
            'source'=>1,
            'channel' => $data['source'],
            'media' => '网易新闻客户端',
//            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }
}
$response_result = $final_result;
file_put_contents('163_applist', var_export($final_result, true));
//file_put_contents("163_applist.txt",var_dump($final_result),FILE_APPEND);