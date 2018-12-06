<?php

/**
 * @filename sougou_zhidao_article.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-4  17:23:40
 * @updatetime 2016-8-4  17:23:40
 * @version 1.0
 * @Description
 * 搜狗问问解析
 * 
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://127.0.0.1:8888/yuqing_fengtian/zhuaqu/spider_center/wii_spider1/zhidao_list/spider_result/ffe86b125bf486cc77f848f173b579fd";

$html = file_get_contents($collect_download_url);
//print_r($html);
$final_result = array();

$main_reg = '/<div id="main" class="main">(.*)<\/div>/s';
preg_match($main_reg, $html, $main_result);
//print_r($main_result[0]);

$li_reg = '/<div class="vrwrap">(.*?)<\/div>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
$num = count($li_result);

 for($i = 1 ; $i < $num ; $i++ )
 {
     //print_r($li_result[$i][1]);
     $array_list[$i] = $li_result[$i][1];

 }
 //$array_list = array();
foreach($array_list as $li) {
    $url_reg = '/<a target="_blank" href="(.*?)"/s';
    $title_reg = '/<a[^>]+>(.*?)<\/a>/s';        // 标题
    $summary_reg = '/<span>(.*?)<\/span>/s';      // 内容
    $time_reg = '/提问时间: (.*?)&nbsp;-&nbsp;/s';

    preg_match($url_reg, $li, $url_result);
    preg_match($title_reg, $li, $title_result);
    preg_match($summary_reg, $li, $summary_result);
    preg_match($time_reg, $li, $time_result);

    $final_result[] = array(
        'url' => $url_result[1],     // url处理
        'title' => trim(strip_tags($title_result[1])),        // 删除标签
        'time' => strtotime($time_result[1]),
        'summary' => trim(strip_tags($summary_result[1])),         // 删除标签和前后空格
        'author' => '',
        'source'=>1,
        'media' => '搜狗问问',
    );
}
//print_r($final_result);
$response_result = $final_result;