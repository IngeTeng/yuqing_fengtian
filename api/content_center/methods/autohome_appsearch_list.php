<?php

/**
 * @filename autohome_appsearch_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-15  20:04:11
 * @updatetime 2016-11-15  20:04:11
 * @version 1.0
 * @Description
 * 汽车之家app搜索
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/appsearch_list/spider_result/869c84b63b5cde601eb079e3f2ce55a2";

$html = file_get_contents($collect_download_url);
$final_result = array();
file_put_contents('auto-apps', $html);
if (false === strpos($html, 'Forbidden')) {
    $main_reg = '/\(\{(.*?)\}\)/s'; 

    // 最外层匹配
    preg_match($main_reg, $html, $main_result);
    $main = json_decode('{'.$main_result[1].'}', true);
    //print_r($main);
    $li_result = $main['result'];

    foreach($li_result as $value) {


        $final_result[] = array(
            'url' =>  $value['Link'],
            'title'   => trim(strip_tags($value['Title'])),
            'content' => trim(strip_tags($value['Summary'])),
            'from' => '汽车之家',
            'time' =>  strtotime($value['Date'].' 08:00:00'),
            'channel' => 'Autohome',
        );
    }
}
//print_r($final_result);
$response_result = $final_result;