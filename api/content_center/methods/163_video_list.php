<?php

/**
 * @filename 163_vedio_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-9  10:42:16
 * @updatetime 2016-8-9  10:42:16
 * @version 1.0
 * @Description
 * 网易视频解析
 * 
 */


$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/video_list/spider_result/0fa8c49dfc05430dc4739e3a0c35c2d2";

$html = file_get_contents($collect_download_url);
$final_result = array();

$li_reg = '/<div class="v_list_item auto_news_item ">(.*?)<\/div>/s';
preg_match_all($li_reg, $html, $li_result, PREG_SET_ORDER);
foreach($li_result as $li) {
    $title_reg = '/<h3>(.*?)<\/h3>/';
    $url_reg = '/href="([^"]+)"/';
    $time_reg = "/<span>(.*?)<\/span>/";

    $author_reg='/<p class="item_info">来源：(.*?)<span>';

    preg_match($title_reg, $li[0], $title_result);
    preg_match($url_reg, $li[0], $url_result);
    preg_match($time_reg, $li[0], $time_result);

    preg_match($author_reg, $li[0], $author);
    $title = iconv('gbk', 'utf-8', $title_result[1]);

    $final_result[] = array(
        'title' => trim(strip_tags($title)),
        'url' => $url_result[1],
        'author' => $author,
        'source' => 1,
        'time' => strtotime($time_result[1]),
        'media' => '网易视频',
    );
}

$response_result = $final_result;
file_put_contents('wangyivideo-lists', var_export($final_result, true));