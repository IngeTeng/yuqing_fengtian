<?php

/**
 * @filename news_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-6-22  17:14:12
 * @updatetime 2016-6-22  17:14:12
 * @version 1.0
 * @Description
 * 解析百度新闻搜索列表 
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/news_list/spider_result/fd1b81854070caa34325626a655d0bd7";

$html = file_get_contents($collect_download_url);
//file_put_contents('baidu-html', $html);

$main_reg = '/<div id="content_left">(.*)<\/div>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
$final_result = array();
//file_put_contents('main', var_export($main_result, true));
$li_reg = '/div class="result" (.*?)百度快照/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//file_put_contents('li', var_export($li_result, true));
foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/<a href="(.*?)"/s';
    $title_reg = '/<h3 class="c-title">(.*?)<\/h3>/s';
    $abstract_reg = '/<\/p>(.*?)<span class="c-info">/s';
    $from_reg = '/<p class="c-author">(.*?)&nbsp;&nbsp;/s';
    $time_reg = '/&nbsp;&nbsp;(.*?)<\/p>/s';

    preg_match($url_reg, $value[0], $url_result);

    preg_match($title_reg, $value[0], $title_result);
    preg_match($abstract_reg, $value[0], $abstract_result);
    preg_match($from_reg, $value[0], $from_result);
    preg_match($time_reg, $value[0], $time_result);

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
    //file_put_contents('title', trim(strip_tags($title_result[1])));
    //file_put_contents('url', var_export($url_result, true));
    //file_put_contents('from', var_export($from_result, true));
    if(empty($from_result[1]) or empty($pubtime) or empty($title_result[1]) or empty($url_result[1])) {
		continue;
	}
	
	if(strlen($from_result[1]) > 30) {
        continue;
    }
	
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'abstract' => trim(strip_tags($abstract_result[1])),
        'from' => $from_result[1],
        'source'=>1,
        'author' =>$from_result[1],
        'time' => $pubtime,
        'channel' => '百度新闻',
    );
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('baidu-news_list', var_export($final_result, true));
