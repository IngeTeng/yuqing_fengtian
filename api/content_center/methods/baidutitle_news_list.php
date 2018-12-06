<?php

/**
 * @filename baidutitle_news_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-13  18:44:17
 * @updatetime 2016-11-13  18:44:17
 * @version 1.0
 * @Description
 * 百度新闻标题搜索
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing2/wii_spider/news_list/spider_result/29ae08c2897311966acad41c17f405ab";

$html = file_get_contents($collect_download_url);

$main_reg = '/<div id="content_left">(.*)<\/div>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
$final_result = array();
//file_put_contents('main', var_export($main_result, true));
$li_reg = '/<div class="result title(.*?)<\/div>\s*<\/div>/s';//\s是指空白，包括空格、换行、tab缩进等所有的空白

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//file_put_contents('li', var_export($li_result, true));
foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/<a href="(.*?)"/s';
    $title_reg = '/<h3 class="c-title">(.*?)<\/h3>/s';
    $from_reg = '/<div class="c-title-author">(.*?)&nbsp;&nbsp;/s';
    $time_reg = '/&nbsp;&nbsp;(.*?)&nbsp;&nbsp;/s';

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($from_reg, $value[0], $from_result);
    preg_match($time_reg, $value[0], $time_result);
    
    if(empty($time_result[1])) {
        $time_reg = '/&nbsp;&nbsp;(.*?)<\/div>/s';
	    preg_match($time_reg, $value[0], $time_result);
    }


    // 处理时间
    $time_str = '';
    if(!empty($time_result[1])) {
        $time_str = $time_result[1];
        $time_str = str_replace("年","-",$time_str);
        $time_str = str_replace("月","-",$time_str);
        $time_str = str_replace("日","",$time_str);
        $p = strpos($time_str, "分");
        $sec = 60;
        if(!$p) {
            $p = strpos($time_str, "小时");
            $sec = 3600;
        }
        if($p > 0) {
            $dd = substr($time_str, 0, $p);
            $pubtime = time() - $dd * $sec;
        }
        else {
            $pubtime = strtotime($time_str);
        }   
    }
    
    if(empty($from_result[1]) or empty($pubtime) or empty($title_result[1]) or empty($url_result[1])) {
        continue;
    }
    
    $title = trim(strip_tags($title_result[1]));
    
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => $title,
        'abstract' => $title,
        'from' => $from_result[1],
        'author' => $from_result[1],
        'source' => 1,
        'time' => $pubtime,
        'channel' => '百度新闻标题',
    );
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('baidutitle-list', var_export($final_result, true));