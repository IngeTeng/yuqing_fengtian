<?php

/**
 * @filename tencent_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  14:28:24
 * @updatetime 2016-8-15  14:28:24
 * @version 1.0
 * @Description
 * 腾讯api解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/1f201068b92d445017560a9d3a03604b";

$json_str = file_get_contents($collect_download_url);
$final_result = array();

if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);
    $data_list = $json_array['idlist'][0]['newslist'];

    foreach($data_list as $data) {
        
        $content_url = "http://r.inews.qq.com/getSimpleNews/19_android_4.4.3/news_news_top/{$data['id']}/wifi/720?devid=860308025820347&uid=d59e160a-fd48-4319-92c9-76a3942e048f&Cookie=%20lskey%3D%3B%20luin%3D%3B%20logintype%3D0%20&qn-rid=1953998610&store=109&hw=Xiaomi_MI2&devid=860308025820347&qn-sig=b78400cc05c356e13f2eefcfcfb5f02e&mac=ac%253Af7%253Af3%253A1e%253Af9%253Ae0&appver=19_android_4.4.3&qqnetwork=wifi&sceneid=73387&imsi=460019328236769&apptype=android&";
        
        $final_result[] = array(
            'id' => $data['id'],
            'title' => $data['title'],
            'url' => $content_url,
            'author' => $data['uinnick'],
            'source' => 1,
            'time' => $data['timestamp'],
            'channel' => $data['source'],
            'media' => '腾讯新闻客户端',
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }    
}

$response_result = $final_result;