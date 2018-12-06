<?php

/**
 * @filename soku_video_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  20:58:30
 * @updatetime 2016-8-3  20:58:30
 * @version 1.0
 * @Description
 * 优酷视频搜索
 * 
 */

//$collect_download_url = $post_obj['collect_download_url'];
$collect_download_url = "http://localhost/yuqing2/wii_spider/video_list/spider_result/2d28142ec28ff4f8f634f5c9c12a0fcb";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="sk_result"(.*?)<div class="sk_more/s';
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
$li_reg = '/<div class="v"(.*?)<\/div>\s+<\/div>\s+<\/div>\s+<\/div>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach($li_result as $li) {
    $title_reg = '/title="(.*?)"/';
    $url_reg = '/href="(.*?)"/';
    $time_reg = '/<span class="r">(.*?)<\/span>/';

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
        'time' => $public_time,
        'media' => '优酷视频',
    );
}
//print_r($final_result);
$response_result = $final_result;
