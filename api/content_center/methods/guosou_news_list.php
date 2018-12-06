<?php

/**
 * @filename guotong_news_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl
 * @datetime 2017-11-28  20:10:55
 * @version 1.0
 * @Description
 * 
 * 解析国搜新闻的list
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = 'E:\wamp64\www\yq\wii_spider\news_list\spider_result\fa387c7872f3ae9ae83e0cdf2364322f';
//$collect_download_url = "http://news.so.com/ns?rank=pdate&q=CHR&pn=3";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<ol class="seResult">(.*?)<\/ol>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
// 懒惰匹配    
$li_reg = '/<li class="reItem ">(.*?)(<\/span>|<\/a>)<\/p>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/<a href="(.*?)"/s';
    $title_reg = '/<h2>(.*?)<\/h2>/s';

    // newsinfo 包含下面三个属性
    $newsinfo_reg = '/<p class="snapshot">(.*?)<\/p>/s';
    $from_reg = '/&nbsp;&nbsp;(.*?)<\/span>/s';
    $time_reg = '/<span>(.*?)&nbsp;-/s';
    preg_match($title_reg, $value[0], $title_result);
    preg_match($url_reg, $title_result[0], $url_result);
    
   // preg_match($abstract_reg, $value[0], $abstract_result);

    preg_match($newsinfo_reg, $value[0], $newsinfo_result);

    if(!empty($newsinfo_result[0])) {
        preg_match($from_reg, $newsinfo_result[0], $from_result);
        preg_match($time_reg, $newsinfo_result[0], $time_result);
    }
    else {
        preg_match($from_reg, $value[0], $from_result);
        preg_match($time_reg, $value[0], $time_result);
    }
    $time = strtotime($time_result[1].' 08:00:00');
   //print_r($time_result);
   //print_r($from_result);
    //print_r($title_result);
    //print_r($url_result);
    if(empty($from_result)){
        $from = '';
    }else{
        $from = $from_result[1];
    }
    if(empty($time_result[1]) or empty($title_result[1]) or empty($url_result[1])) {
        //print_r($value);
		continue;
	}
	//$n = strlen($from_result[1]);
   // $from = substr($from_result[1], 0, $n - 2);
	
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'abstract' => trim(strip_tags($title_result[1])),
        'from' => $from,
        'author' => $from,
        'source' => 1,
        'time' => $time,
        'channel' => '国搜新闻',
    );
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('guosuoNews-list', var_export($final_result, true));
