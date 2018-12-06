<?php

/**
 * @filename zaker_appsearch_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-10  20:39:01
 * @updatetime 2016-11-10  20:39:01
 * @version 1.0
 * @Description
 * zaker搜索
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yq/wii_spider/appsearch_list/915afc899950dfb17226720204393d78";

$html = file_get_contents($collect_download_url);
//$html = iconv('UTF-8', 'GBK', $html);
$final_result = array();

$main_reg = '/<div id="list_box">(.*?)<div class="btn_box"/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
// 懒惰匹配    
$li_reg = '/<div href="#" class="list">(.*?)<\/footer>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {
    //print_r($value);
    // 构建正则表达式
    $url_reg = '/<a href="(.*?)"/s';
    $title_reg = '/<h2 class="tit">(.*?)<\/h2>/s';
    $content_reg = '/<article class="infos">(.*?)<\/article>/s';
    $time_reg = '/<span class="url">(.*?)<\/span>/s';

    preg_match($title_reg, $value[0], $title_result);
    preg_match($url_reg, $value[0], $url_result);
    
    preg_match($content_reg, $value[0], $content_result);
    preg_match($time_reg, $value[0], $time_result);
    
    // 处理时间
    $time_str = '';
    if(!empty($time_result[1])) {
        $time_str = $time_result[1]. ' 08:00:00';
        $pubtime = strtotime($time_str);
    }else{
        continue;
    }
    
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'from' => '搜狐手机端',
        'author'=>"",
        'source'=>1,
        'time' => $pubtime,
        //'content' => iconv('UTF-8', 'GBK', trim(strip_tags($content_result[1]))),
    );
}
//print_r($final_result);
$response_result = $final_result;