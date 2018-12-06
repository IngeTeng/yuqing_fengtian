<?php

/**
 * @filename sohu_appsearch_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2016-12-17  17:49:01
 * @updatetime 
 * @version 1.0
 * @Description
 * 搜狐新闻APP搜索
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing2/wii_spider/appsearch_list/spider_result/280e815069a3568184729f5f9489f395";
//
$content_url = 'http://3g.k.sohu.com/t/n%s';//抓取到的结果里没有有效的url

$json_str = file_get_contents($collect_download_url);
//print_r($json_str);
$final_result = array();
if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);
    $data_list  = $json_array['resultList'];
    //print_r($data_list);
    
    
    foreach($data_list as $data) {
       
            //print_r($data);
            if($data['newsType'] != 3) {
                 continue;
            }
            $newsid = $data['newsId'];
            $url    = sprintf($content_url, $newsid);
            //print_r($url);
            $final_result[] = array(
                'title'   => $data['title'],
                'url'     => $url,
                'time'    => $data['time'],
                'content' => $data['description'],
                'from'    => '搜狐APP搜索——'.$data['media'],
                'channel' => '',
            );
    }    
}
//print_r($final_result);
$response_result = $final_result;
