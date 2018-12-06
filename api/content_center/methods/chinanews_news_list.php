<?php

/**
 * @filename 12365auto.php
 * @encoding UTF-8
 * @author WiiPu CzRzChao
 * @createtime 2016-10-11  23:14:00
 * @updatetime 2016-10-11  23:14:00
 * @version 1.0
 * @Description
 *
 */

$collect_download_url = $post_obj['collect_download_url'];

$html = file_get_contents($collect_download_url);;
$final_result = array();

$main_reg = '/<div id="news_list">(.*?)<div id="page_bar">/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配
$li_reg = '/<table cellpadding="0" cellspacing="0">(.*?)<\/table>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);




foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/<a href="(.*?)"/s';

    $title_reg = '/target="_blank">(.*?)<\/a>/s';


    $abstract_reg = '/<li class="news_content">(.*?)<\/li>/s';


    $time_reg = '/&nbsp;&nbsp;(.*?)<\/li>/s';


    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($abstract_reg, $value[0], $abstract_result);


    preg_match($time_reg, $value[0], $time_result);


    $final_result[] = array(
        'url' => $url_result[1],
        'title' => strip_tags($title_result[1]),
        'abstract' => strip_tags($abstract_result[1]),
        'from' => '中国新闻网',
        'source'=>1,
        'author' => '',
        'time' => strtotime(trim($time_result[1])),
        'channel' => '中国新闻网',
    );
}

$response_result = $final_result;
file_put_contents('chinanews-news-list', var_export($final_result, true));