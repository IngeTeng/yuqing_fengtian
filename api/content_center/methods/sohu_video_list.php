<?php

/**
 * @filename sohu_video_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-4  10:59:16
 * @updatetime 2016-8-4  10:59:16
 * @version 1.0
 * @Description
 * 搜狐视频解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/video_list/spider_result/4dd76770e1889ac81cecbc247e63633d";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<ul class="list170 cfix"(.*?)<\/ul>/s';
preg_match($main_reg, $html, $main_result);

$li_reg = '/<li>(.*?)<\/li>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $li) {
    $title_reg = '/title="([^"]+)"/';
    $url_reg = '/href="([^"]+)"/';
    $time_reg = '/class="tcount"[^\/]+>(.*?)<\/a>/';
    $author_reg='/title="(.*?)" class="name"/';
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
        'media' => '搜狐视频',
    );
}

$response_result = $final_result;
file_put_contents('sohu_video-lists', var_export($final_result, true));