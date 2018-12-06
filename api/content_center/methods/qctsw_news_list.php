<?php

/**
 * @filename qctsw_news_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-10-11  22:49:59
 * @updatetime 2016-10-11  22:49:59
 * @version 1.0
 * @Description
 * 
 */


$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/news_list/spider_result/900ba338efe31ea6dcf99afdbbf80229";
$html = file_get_contents($collect_download_url);
$final_result = array();
//print_r($html);
$main_reg = '/<div class="listcon">(.*?)<\/div>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
// 懒惰匹配    
$li_reg = '/<li>(.*?)<\/li>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
unset($li_result[0]);	// 删除第一个li

foreach($li_result as $value) {
    
    
    // 构建正则表达式
    $url_reg = '/<a href="(.*?)">/s';
    $title_reg = '/<a href="[^>]+>(.*?)<\/a>/s';

    $time_reg = '/投诉人:[^&]+&nbsp;(.*?)<\/p>/s';

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($time_reg, $value[0], $time_result);
    
    $time_str = str_replace('/', '-', $time_result[1]) . ' 08:00:00';
    $url = 'http://www.qctsw.com'. str_replace('&amp;', '&', $url_result[1]);
    $title = trim(strip_tags($title_result[1]));
    
    $final_result[] = array(
        'url' => $url,
        'title' => $title,
        'abstract' => $title,
        'from' => '汽车投诉网',
        'author' => "",
        'source' => 0,
        'time' => strtotime($time_str),
        'channel' => '汽车投诉网',
    );
}
//print_r($final_result);
$response_result = $final_result;