<?php

/**
 * @filename cheshi_news_list.php 
 * @encoding UTF-8 
 * @author WiiPu hf
 * @createtime 2016-11-11  16:59:55
 * @updatetime 2016-11-11  16:59:55
 * @version 1.0
 * @Description
 * 网上车市解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing_fengtian/ifeng.htm";

$html = file_get_contents($collect_download_url);
$final_result = array();
//print $html;

// 懒惰匹配    
$li_reg = '/<div class="searchResults">(.*?)<\/div>/s';

// 第四个参数用于所有结果排序

preg_match_all($li_reg, $html, $li_result);
//print_r($li_result[1]);
foreach($li_result[1] as $value) {

    // 构建正则表达式
    $url_reg = '/href="(.*?)"/s';
    $title_reg = '/<a href="[^"]*"[^>]*>(.*)<\/a>/';
    $abstract_reg = '/<p>(.*?)<\/p>/s';
    $time_reg = '/<font color="#1a7b2e">(.*?)<\/font>/s';

    preg_match($url_reg, $value, $url_result);
   // print_r($url_result[1]);
    preg_match($title_reg, $value, $title_result);
   // print_r($title_result);
    preg_match($abstract_reg, $value, $abstract_result);
    preg_match($time_reg, $value, $time_result);
    $time_preg='/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/';
    preg_match($time_preg,$time_result[1],$time_res);
 //   print_r($time_res);
   // print_r($time_result);
    if(empty($time_result[1]) or empty($title_result[1]) or empty($url_result[1])) {
        continue;
    }
	
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'abstract' => trim(strip_tags($abstract_result[1])),
        'from' => '凤凰资讯',
        'author' => '',
        'source' => 1,
        'time' => strtotime($time_res[1]),
        'channel' => '凤凰资讯',
    );
}

$response_result = $final_result;
print_r($response_result);
file_put_contents('ifengsearch_news_list', var_export($final_result, true),FILE_APPEND);
