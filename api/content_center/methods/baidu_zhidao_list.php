<?php

/**
 * @filename baidu_zhidao_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-16  14:29:30
 * @updatetime 2016-8-16  14:29:30
 * @version 1.0
 * @Description
 * 百度知道解析
 * 
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://127.0.0.1:8888/yuqing_fengtian/zhuaqu/spider_center/wii_spider1/zhidao_list/spider_result/ef98093c9d1970f3ff9c0b7abff66496";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="list-inner">(.*?)<\/div>/s';
preg_match($main_reg, $html, $main_result);

$li_reg = '/<dl class="dl(.*?)<\/dl>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $li) {
    $li_content = iconv("gbk", "utf-8", $li[0]);

    $url_reg = '/<a href="(.*?)"/s';                
    $title_reg = '/<a href="[^>]+>(.*?)<\/a>/s';        // 标题
    $summary_reg = '/<dd class="dd summary">(.*?)<\/dd>/s';
    $answer_reg = '/<dd class="dd answer">(.*?)<\/dd>/s';
    $time_reg = '/<span class="mr-8">(.*?)<\/span>/s';     // 发布时间

    preg_match($url_reg, $li_content, $url_result);
    preg_match($title_reg, $li_content, $title_result);
    preg_match($summary_reg, $li_content, $summary_result);
    preg_match($answer_reg, $li_content, $answer_result);
    preg_match($time_reg, $li_content, $time_result);

    $summary = empty($summary_result)? '':strip_tags($summary_result[1]);
    $summary .= strip_tags($answer_result[1]);
    $title = trim(strip_tags($title_result[1]));
    $time = strtotime($time_result[1]. ' '. date('H:i:s'));

    $final_result[] = array(
        'url' => str_replace('&amp', '&',$url_result[1]),     // url处理
        'title' => $title,        
        'time' => $time,
        'summary' => $summary,         
        'author' => '',
        'source'=>1,
        'media' => '百度知道',
    );
}
print_r($final_result);
$response_result = $final_result;
//file_put_contents('baiduzhidao-result', var_export($final_result, true));