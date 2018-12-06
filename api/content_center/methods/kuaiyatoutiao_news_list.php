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
//$collect_download_url = "http://localhost/yuqing/wii_spider/news_list/spider_result/0ea502e9ae774b85d779ae7c60ad5906";
//$collect_download_url = "http://minivideosearch.dftoutiao.com/search_pc/searchnews?jsonpcallback=jQuery18309115080178963126_1511741274300&keywords=%25E5%2587%25AF%25E7%25BE%258E%25E7%2591%259E&stkey_zixun=&lastcol_zixun=&splitwordsarr=&uid=14988936424175451&qid=k002&softtype=toutiao&softname=DFTT&browser_type=chrome62.0.3202.75&pixel=1600*900&_=1511741539696";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/"data":\[(.*?)\]\}\}\)/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/\{(.*?)"comment_count"/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {
    //print_r($value);
    // 构建正则表达式
    $url_reg = '/"url":"(.*?)"/s';
    //$title_reg = '/target="_blank">(.*?)<\/a>/s';
    $title_reg = '/"title":"(.*?)"/s';
   // $abstract_reg = '/<p class="content">(.*?)<\/p>/s';

    // newsinfo 包含下面三个属性
    //$newsinfo_reg = '/<p class="newsinfo">(.*?)<\/p>/s';
    $from_reg = '/"source":"(.*?)"/s';
    $time_reg = '/"date":"(.*?)"/s';

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    //preg_match($abstract_reg, $value[0], $abstract_result);

    preg_match($time_reg, $value[0], $time_result);

    preg_match($from_reg, $value[0], $from_result);

    //print_r($url_result);
    if(empty($time_result[1]) or empty($title_result[1]) or empty($url_result[1])){
		continue;
	}
	//$n = strlen($from_result[1]);
    $from = $from_result[1];


	
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        //'abstract' => trim(strip_tags($abstract_result[1])),
        'from' => $from,
        'author' => $from,
        'source' => 1,
        'time' => strtotime($time_result[1]),
        'channel' => '快压头条',
    );
}
//print_r($final_result);

$response_result = $final_result;
file_put_contents('kyttnews-lists', var_export($final_result, true));