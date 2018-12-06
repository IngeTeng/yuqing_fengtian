<?php

/**
 * @filename cheshi_news_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-11  16:59:55
 * @updatetime 2016-11-11  16:59:55
 * @version 1.0
 * @Description
 * 网上车市解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/news_list/spider_result/0a1beb9e5795b62b776398c810c811c8";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="force">(.*?)<div class="page">/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/<div class="result">(.*?)<\/div>\s+<\/div>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/href="(.*?)"/s';
    $title_reg = '/<h3>(.*?)<\/h3>/s';
    $abstract_reg = '/<p>(.*?)<\/p>/s';
    $time_reg = '/&nbsp;&nbsp;&nbsp;&nbsp;(.*?)\s+<\/div>/s';

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($abstract_reg, $value[0], $abstract_result);
    preg_match($time_reg, $value[0], $time_result);
    
    if(empty($time_result[1]) or empty($title_result[1]) or empty($url_result[1])) {
        continue;
    }
	
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'abstract' => trim(strip_tags($abstract_result[1])),
        'from' => '网上车市',
        'author' => '',
        'source' => 1,
        'time' => strtotime($time_result[1]),
        'channel' => '网上车市',
    );
}

$response_result = $final_result;
file_put_contents('cheshinews-list', var_export($final_result, true));
