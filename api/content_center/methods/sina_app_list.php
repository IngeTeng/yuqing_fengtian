<?php

/**
 * @filename sina_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-14  15:46:07
 * @updatetime 2016-8-14  15:46:07
 * @version 1.0
 * @Description
 * 新浪api解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/dfb75b0f723fd7f692f8e53ee9780394";

$content_url = 'http://api.sina.cn/sinago/articlev2.json?id=%s&uid=0b3cc567b3eac3fd&wm=b207&oldchwm=14010_0001&imei=860308025820347&from=6044095012&postt=news_news_ent_feed_3&chwm=14010_0001';

$json_str = file_get_contents($collect_download_url);
$json_array = json_decode($json_str, true);
$final_result = array();

$data_list = $json_array['data']['list'];
foreach($data_list as $data) {
    
    if(empty($data['pubDate'])) {
        continue;
    }
    
    $final_result[] = array(
        'id' => $data['id'],
        'title' => $data['title'],
        'url' => sprintf($content_url, $data['id']),
        'time' => $data['pubDate'],
        'author'=>"",
        'source'=>1,
        'media' => '手机新浪网',
        'collect_from_site' => $post_obj['collect_from_site'],
        'channel' => $data['source'],
    );
}

$response_result = $final_result;