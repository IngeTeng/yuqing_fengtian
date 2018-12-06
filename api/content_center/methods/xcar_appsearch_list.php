<?php

/**
 * @filename xcar_appsearch_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2016-11-18  16:16:01
 * @updatetime 
 * @version 1.0
 * @Description
 * 爱卡汽车appsearch搜索结果解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = 'http://localhost/yuqing/wii_spider/appsearch_list/spider_result/f44791aa890cca3aac6b03cecab1ed60'; //本地调试用

$html         = file_get_contents($collect_download_url);
$final_result = array();
file_put_contents('xcar', $html);
//匹配全部新闻的最外层 /s:只匹配最先的
$main_reg = '/<div class="alt-wrap">(.*?)<div class="look-more">/s'; 

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配 ，匹配每个新闻的最外层
$li_reg   = '/<li>(.*?)<\/li>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg     = '/<a href="(.*?)"/s';
    $title_reg   = '/<div class="alt-tit">(.*?)<span class="time">/s';
    $content_reg = '/<div class="alt-info">(.*?)<i class="btn/s';
    //$from_reg  = '/<span class="article-source">(.*?)<\/span>/s';
    $time_reg    = '/<span class="time">(.*?)<\/span>/s';

    preg_match($url_reg,     $value[0], $url_result);
    preg_match($title_reg,   $value[0], $title_result);
    preg_match($content_reg, $value[0], $content_result);
    //preg_match($from_reg,  $value[0], $from_result);
    //$from_result = ;
    preg_match($time_reg,    $value[0], $time_result);
    
    if(empty($content_result[1])) {
        $content = trim(strip_tags($title_result[1]));
    }    else {
        $content = trim(strip_tags($content_result[1]));
    }

    $final_result[] = array(
        'url'     => $url_result[1],
        'title'   => trim(strip_tags($title_result[1])),
        'content' => $content,
        'from'    => '爱卡汽车',
        'time'    => strtotime($time_result[1]),
        'channel' => '爱卡汽车',
    );
}

$response_result = $final_result;
file_put_contents('xcar_re', var_export($final_result,true));