<?php

/**
 * @filename pcauto_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  15:23:51
 * @updatetime 2016-8-15  15:23:51
 * @version 1.0
 * @Description
 * 太平洋汽车网api解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/8323268e9359e58901840e0809f55135";

$json_str = file_get_contents($collect_download_url);
$final_result = array();
if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);
    $data_list = $json_array['data'];

    foreach($data_list as $data) {
        
        $content_url = "http://mrobot.pcauto.com.cn/v3/cms/articles/{$data['id']}";
        
        $final_result[] = array(
            'id' => $data['id'],
            'title' => $data['title'],
            'url' => $content_url,
            'author' => "",
            'source' => $data['ups'],
            'time' => strtotime($data['pubDate']. ' 08:00'),
            'channel' => isset($data['channelName']) ? $data['channelName']:'太平洋汽车网手机客户端',
            'media' => '太平洋汽车网手机客户端',
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }    
}

$response_result = $final_result;

