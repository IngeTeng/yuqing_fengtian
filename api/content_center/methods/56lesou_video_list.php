<?php

/**
 * @filename video_list.php 
 * @encoding UTF-8 
 * @author WiiPu tjl 
 * @createtime 2017-12-13  09:10:12
 * @updatetime 2017-12-13  
 * @version 1.0
 * @Description
 * 56乐搜视频列表解析
 * 
 */

// 获取缓存好的html


$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/tudou/spider_result/6fad3063dbc8105cd3340536aaf7f2d1";
//$collect_download_url = "http://so.56.com/mts?wd=%E5%87%AF%E7%BE%8E%E7%91%9E&c=0&v=0&length=0&limit=0&site=0&o=3&p=1&st=&suged=&filter=0";
$html = file_get_contents($collect_download_url);
$final_result = array();
//print_r($html);

$main_reg = '/<div class="ssList area">(.*?)<div class="ssPages/s';
preg_match($main_reg, $html, $main_result);

$li_reg = '/<li>(.*?)<\/li>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $li) {
    $title_reg = '/title="(.*?)"/';
    $url_reg = '/href="(.*?)"/';
    $time_reg = '/"100074">(.*?)前<\/a>/';

    preg_match($title_reg, $li[0], $title_result);
    preg_match($url_reg, $li[0], $url_result);
    preg_match($time_reg, $li[0], $time_result);

    // 处理时间
    $d = $time_result[1];
    $p = strpos($d, "分");
    $sec = 60;
    if(!$p) {
        $p = strpos($d, "小时");
        $sec = 3600;
    }
    if(!$p) {
        $p = strpos($d, "天");
        $sec = 86400;
    }
    if(!$p) {
        $p = strpos($d, "月");
        $sec = 86400 * 30;
    }
    if(!$p) {
        $p = strpos($d, "年");
        $sec = 86400 * 365;
    }

    if($p > 0) {
        $dd = substr($d, 0, $p);
        $public_time = time() - $dd * $sec;
    }
    else {
        $public_time = time();		
    }

    $final_result[] = array(
        'title' => trim(strip_tags($title_result[1])),
        'url' => $url_result[1],
        'author' => "",
        'source' => 1,
        'time' => $public_time,
        'media' => '56乐搜视频',
    );
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('51lesouvideo-lists', var_export($final_result, true));