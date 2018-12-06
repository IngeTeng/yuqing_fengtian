<?php

/**
 * @filename 360_zhidao_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-9  15:01:26
 * @updatetime 2016-8-9  15:01:26
 * @version 1.0
 * @Description
 * 360问答解析
 * 
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing2/wii_spider/zhidao_list/spider_result/232c5741d5d8710f33db8b7bfc8d3895";
//$collect_download_url = "http://127.0.0.1:8888/yuqing_fengtian/zhuaqu/spider_center/wii_spider1/zhidao_list/spider_result/ff0cd7079d14c254a3b308b3080bc771";

$html = file_get_contents($collect_download_url);
$final_result = array();
//print_r($html);
$main_reg = '/<ul class="question-list">(.*?)<\/ul>/s';
preg_match($main_reg, $html, $main_result);

$li_reg = '/<li(.*?)<\/div><\/li>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $li) {
    $url_reg = '/<a href="(.*?)"/s';                
    $title_reg = '/<a[^>]+>(.*?)<\/a>/s';        // 标题
    $time_reg = '/<span class="gray3">(.*?)<\/span>/s';     // 发布时间

    preg_match($url_reg, $li[0], $url_result);
    preg_match($title_reg, $li[0], $title_result);
    preg_match($time_reg, $li[0], $time_result);

    $title = trim(strip_tags($title_result[1]));
    $time_str = $time_result[0];
    //print_r($time_result);
    $p = strpos($time_str, "今天");
    if($p){
        $time = time() - time()%(3600*3);
    }else{
        $time = strtotime(str_replace('.', '-', $time_result[1]));
    }
    

    $final_result[] = array(
        'url' => 'http://wenda.so.com'. $url_result[1],     // url处理
        'title' => $title,        
        'time' => $time,
        'summary' => $title,         
        'author' => '',
        'source'=>1,
        'media' => '360问答',
    );
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('360zhidao-result', var_export($final_result, true));