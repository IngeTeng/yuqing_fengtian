<?php

/**
 * @filename yidianzx_appsearch_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2016-11-14  22:09:01
 * @updatetime 
 * @version 1.0
 * @Description
 * yidianzx搜索
 * 
 */
// ini_set('display_errors',1);            //错误信息  
// ini_set('display_startup_errors',1);    //php启动错误信息  
// error_reporting(0);                    //打印出所有的 错误信息  
// ini_set('error_log', dirname(__FILE__) . '/yidianzix_log.txt'); //将出错信息输出到一个文本文件 

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = 'http://localhost/yuqing2/wii_spider/appsearch_list/spider_result/e714c498e6d6837785b82adfeda759fc';
//$collect_download_url = 'http://www.yidianzixun.com/home/q/news_list_for_keyword?display=%E9%80%8D%E5%AE%A2&cend=50&word_type=token';

$html = file_get_contents($collect_download_url);
$final_result = array();
//file_put_contents('yidianzx-html', $html);

$main_reg = '/"items":\[(.*?)\],"query"/s'; ///s值匹配最先的
// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/\{(.*?)\}/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
if(empty($main_result)){//两种情况
    $main_reg = '/"result":\[(.*?)\],"fresh_count"/s'; ///s值匹配最先的
    preg_match($main_reg, $html, $main_result);
    $li_reg = '/\{"extra":(.*?)"finish_play"/s';
    // 第四个参数用于所有结果排序
    preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
}
// file_put_contents('main', var_export($main_result, true));
// file_put_contents('li', var_export($li_result, true));
//print_r($li_result);
foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg   = '/"url":"(.*?)"/s';
    $title_reg = '/"title":"(.*?)"/s';
    $content_reg = '/"summary":"(.*?)"/s';
    $from_reg  = '/"source":"(.*?)"/s';
    $time_reg  = '/"date":"(.*?)"/s';

    preg_match($url_reg,   $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($content_reg, $value[0], $content_result);
    preg_match($from_reg,  $value[0], $from_result);
    preg_match($time_reg,  $value[0], $time_result);
    
    if(empty($content_result[1])) {
        $content = trim(strip_tags($title_result[1]));
    }    else {
        $content = trim(strip_tags($content_result[1]));
    }
    if(empty($url_result)){
        continue;
    }

    //删除url中的&up=...
    //str_replace('&amp;', '&', $url_result[1]);
    //$preg = '/[\&|\?]up=[^&]+/s';
   // $url_result[1] = preg_replace($preg, '', $url_result[1]);
   // print_r($url_result);

    $final_result[] = array(
        'url'     => $url_result[1],
        'title'   => trim(strip_tags($title_result[1])),
        'content' => $content,
        /**************/

        'author'=>$from_reg,
        'source'=>1,
        /******************/
        'from'    => '一点资讯',
        'time'    => strtotime($time_result[1]),
        'channel' => $from_result[1],
    );
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('yidianzx-lists', var_export($final_result, true));