<?php

/**
 * @filename ku6_video_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-16  16:08:35
 * @updatetime 2016-8-16  16:08:35
 * @version 1.0
 * @Description
 * ku6视频列表解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/video_list/spider_result/c5c3b69cc4a9f4acc4e11dff102febf8";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<ul class="cfix ckl_cktpp">(.*?)<\/ul>/s';
preg_match($main_reg, $html, $main_result);

$li_reg = '/<li>(.*?)<\/li>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $li) {
    $title_reg = '/title="(.*?)"/';
    $url_reg = '/<a href="(.*?)"/';
    $time_reg = '/<p class="ckl_ttp">发布于：(.*?)<\/p>/';

    preg_match($title_reg, $li[0], $title_result);
    preg_match($url_reg, $li[0], $url_result);
    preg_match($time_reg, $li[0], $time_result);

    $final_result[] = array(
        'title' => trim(strip_tags($title_result[1])),
        'url' => $url_result[1],
        'time' => strtotime($time_result[1]),
        'author' => '',
        'source' => 1,
        'media' => 'ku6视频',
    );
}

$response_result = $final_result;
file_put_contents('ku6_video-lists', var_export($final_result, true));