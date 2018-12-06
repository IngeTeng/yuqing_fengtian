<?php

/**
 * @filename 315che_news_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-10-11  13:49:48
 * @updatetime 2016-10-11  13:49:48
 * @version 1.0
 * @Description
 * 解析315车投诉
 * 
 */


$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/news_list/spider_result/3ba00c7fcbeabcbf20a773bcc204b79d";
$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="article-list">(.*?)<div class="pages">/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/<li>(.*?)<\/li>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {
    
    
    // 构建正则表达式
    $url_reg = '/<a href="(.*?)" target="_blank">/s';
    $title_reg = '/<h4>(.*?)<\/h4>/s';
    $abstract_reg = '/<p>(.*?)<\/p>/s';

    $time_reg = '/<div class="time">(.*?)<\/div>/s';

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($abstract_reg, $value[0], $abstract_result);
    preg_match($time_reg, $value[0], $time_result);
    
    $time_str = str_replace('&nbsp', ' ', $time_result[1]);

    
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'abstract' => trim(strip_tags($abstract_result[1])),
        'from' => '汽车消费网',
        'time' => strtotime($time_str),
        'channel' => '315che投诉',
    );
}

$response_result = $final_result;