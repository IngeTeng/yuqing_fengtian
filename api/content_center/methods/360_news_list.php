<?php

/**
 * @filename news_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @datetime 2016-6-3  17:10:55
 * @version 1.0
 * @Description
 * 
 * 解析360新闻的list
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/news_list/spider_result/173e60e32355a9188a23deb18b06c4f0";
//$collect_download_url = "http://news.so.com/ns?rank=pdate&q=%E5%A5%95%E6%B3%BD&pn=3";

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/360_news_log.txt'); //将出错信息输出到一个文本文件 

$html = file_get_contents($collect_download_url);
$final_result = array();
//file_put_contents('360-news-html', $html);
$main_reg = '/<ul class="result"(.*?)<\/ul><\/div>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
// 懒惰匹配    
$li_reg = '/<li class="res-list(.*?)<\/p>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/<a *href="(.*?)"/s';
    //$title_reg = '/target="_blank">(.*?)<\/a>/s';
    $title_reg = '/<h3(.*?)<\/h3>/s';
    //$abstract_reg = '/<p class="content">(.*?)<\/p>/s';

    // newsinfo 包含下面三个属性
    $newsinfo_reg = '/<p class="newsinfo">(.*?)<\/p>/s';
    $from_reg = '/<span class="sitename">(.*?)<\/span>/s';
    $time_reg = '/data-pdate="(.*?)">/s';

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
   // preg_match($abstract_reg, $value[0], $abstract_result);

    preg_match($newsinfo_reg, $value[0], $newsinfo_result);

    if(!empty($newsinfo_result[1])) {
        preg_match($from_reg, $newsinfo_result[1], $from_result);
        preg_match($time_reg, $newsinfo_result[1], $time_result);
    }
    else {
        preg_match($from_reg, $value[0], $from_result);
        preg_match($time_reg, $value[0], $time_result);
    }
   //print_r($time_result);
   //print_r($from_result);
    //print_r($title_result);
    //print_r($url_result);
    if(empty($time_result[1]) or empty($from_result[1]) or empty($title_result[1]) or empty($url_result[1])) {
		continue;
	}
	//$n = strlen($from_result[1]);
   // $from = substr($from_result[1], 0, $n - 2);

    
	if($from_result[1] == "未来网") {
		continue;
	}
	
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'abstract' => trim(strip_tags($title_result[1])),
        'from' => $from_result[1],
        'time' => $time_result[1],
        'channel' => '360新闻',
    );
}
//print_r($final_result);
//file_put_contents('360-news-res', var_export($final_result, true));
$response_result = $final_result;
