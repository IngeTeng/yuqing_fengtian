<?php

/**
 * @filename yiche_appsearch_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2016-12-17  14:39:01
 * @updatetime 
 * @version 1.0
 * @Description
 * 易车网APP搜索
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing2/wii_spider/appsearch_list/spider_result/5f8e5cdfd70488ea845e603f5b213384";
//
$content_url = 'http://h5.ycapp.yiche.com/share/medianews/%s.html';//抓取到的结果里没有有效的url

$json_str = file_get_contents($collect_download_url);
//print_r($json_str);
$final_result = array();
if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);
    $data_list  = $json_array['data'];
    //print_r($data_list);
    $n=0;
    
    foreach($data_list as $datas) {

        foreach ($datas as $data) {
       
            //print_r($data);
            if(empty($data['title']) || empty($data['filePath']) || empty($data['publishTime'])) {
                continue;
            }
            $newsid = $data['newsId'];
            $url    = sprintf($content_url, $newsid);
            //print_r($url);
            $final_result[] = array(
                'title'   => $data['title'],
                'url'     => $url,
                'time'    => strtotime($data['publishTime']),
                'content' => $data['title'],
                'author'=>$data['user']['nickName'],
                'source'=>1,
                'from'    => '易车网',
                'channel' => '',
            );
        }
    }    
}
//print_r($final_result);
$response_result = $final_result;
