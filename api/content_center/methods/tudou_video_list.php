<?php

/**
 * @filename video_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-7-30  17:50:12
 * @updatetime 2016-7-30  17:50:12
 * @version 1.0
 * @Description
 * 土豆视频列表解析
 * 
 */

// 获取缓存好的html


$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/tudou/spider_result/6fad3063dbc8105cd3340536aaf7f2d1";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="sk_result"(.*?)<div class="sk_pager/s';
preg_match($main_reg, $html, $main_result);

$li_reg = '/<div class="v"(.*?)<\/div>\s+<\/div>\s+<\/div>\s+<\/div>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $li) {
    $title_reg = '/title="(.*?)"/';
    $url_reg = '/href="(.*?)"/';
    $time_reg = '/<span class="r">(.*?)<\/span>/';
    $author_reg='/_log_ct="4">(.*?)<\/a>/';

    preg_match($title_reg, $li[0], $title_result);
    preg_match($url_reg, $li[0], $url_result);
    preg_match($time_reg, $li[0], $time_result);
    preg_match($author_reg, $li[0], $author);
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
        'time' => $public_time,
        'author' => $author,
        'source' => 1,
        'media' => '土豆视频',
    );
}

$response_result = $final_result;
file_put_contents('tudou_video-lists', var_export($final_result, true));
