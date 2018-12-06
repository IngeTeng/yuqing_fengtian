<?php

/**
 * @filename 360search_news_list.php
 * @encoding UTF-8
 * @author WiiPu CzRzChao
 * @createtime 2016-8-9  11:36:21
 * @updatetime 2016-8-9  11:36:21
 * @version 1.0
 * @Description
 * 360搜索
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/news_list/spider_result/7e5efa6a0cdf83da5f662bc94f09f4ab";
//$collect_download_url = "https://www.so.com/s?a=index&q=%E9%9B%B7%E5%87%8C&pn=5&adv_t=d ";

$html = file_get_contents($collect_download_url);
//file_put_contents('360news', $html);
$final_result = array();

$main_reg = '/<ul class="result"(.*?)<div id="side"/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
// 懒惰匹配

$li_reg = '/<li (.*?)(<\/div>|<\/p>|<\/script>)<\/li>/s';////
//$li_reg = '/<li class="res-list(.*?)<\/div>[<script>TIME.rfTime = +new Date;<\/script>]*[\s]*<\/li>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach($li_result as $value) {
    //print_r($value);
    // 构建正则表达式
    $url_reg = '/<a href="(.*?)>/s';
    $title_reg = '/target="_blank">(.*?)<\/a>/s';
    $abstract_reg = '/<\/h3>(.*?)<p class="res-linkinfo">/s';  ///
    $from_reg = '/data-tp="1" target="_blank">(.*?)<\/a>/s';
    $from_reg2 = '/<h3 class="res-title ">"(.*?)"<\/h3>/s';
    $time_reg2 = '/<span class="gray">(.*?)&nbsp;-&nbsp;/s';
    $time_reg = '/<p class="lst-time">最新更新时间：(.*?)<\/p>/s';////
    $time_reg3 = '/style="display:inline-block">发贴时间：(.*?)<\/p>/s';////

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($abstract_reg, $value[0], $abstract_result);
    preg_match($from_reg, $value[0], $from_result);
    preg_match($time_reg, $value[0], $time_result);



    // print_r($time_result);
    // print_r($from_result);
    // print_r($title_result);
    // print_r($url_result);
    if(empty($time_result[1])){ //情况2 ////
        preg_match($time_reg2, $value[0], $time_result);
    }
    if(empty($time_result[1])){//情况3///
        preg_match($time_reg3, $value[0], $time_result);
    }
    if(empty($from_result[1])){//来源情况2
        preg_match($from_reg2, $value[0], $from_result);
    }
    //print_r($time_result);
    // print_r($from_result);

    if(empty($time_result[1]) or empty($title_result[1]) or empty($url_result[1])) {
        continue;
    }

//    if(empty($from_result[1])){
//        $n = strpos(strip_tags($title_result[1]), '-');
//        $from_result[1] = substr(strip_tags($title_result[1]), $n+1, 30);
//    }

    if(empty($from_result[1])){
        $from_result[1] = '360搜索';
    }
    //print_r($from_result[1]);

    $p = strpos($time_result[1], '分');//x分前
    $sec = 60;
    if(!$p) {
        $p = strpos($time_result[1], '小时');//x小时前
        $sec = 3600;
    }
    if(!$p){
        $p = strpos($time_result[1], '天');//x天前 ///
        $sec = 3600*24;
    }

    if($p > 0) {
        $dd = substr($time_result[1], 0, $p);
        $time = time() - $dd * $sec;
    }
    else {
        $time_str = $time_result[1];
        $time_str = str_replace('年', '-', $time_str);
        $time_str = str_replace('月', '-', $time_str);
        $time_str = str_replace('日', '', $time_str);
        $time = strtotime($time_str);
    }

    if(!empty($abstract_result[1])) {
        $abstract = $abstract_result[1];
    }
    else {
        $abstract = $title_result[1];
    }
    //print_r($url_result[0]);
    preg_match('/data-url="(.*?)"/s', $url_result[1], $url);
    if(empty($url[1])){
        //print_r($url_result);
        preg_match('/<a href="(.*?)"/s', $url_result[0], $url_result2);
        $url[1] =  $url_result2[1];
        //print_r($url_result2);
    }


    $final_result[] = array(
        'url' => urldecode($url[1]), // $url_result[1],
        'title' => strip_tags($title_result[1]),
        'abstract' => trim(strip_tags($abstract)), /////
        'from' => strip_tags($from_result[1]),
        'source'=>1,
        'author' => '',
        'time' => $time,
        'channel' => '360搜索',
    );

}

$response_result = $final_result;
file_put_contents('360search-news-list', var_export($final_result, true));


?>

