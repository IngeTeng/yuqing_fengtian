<?php

/**
 * @filename sougou_news_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-8  17:12:40
 * @updatetime 2016-8-8  17:12:40
 * @version 1.0
 * @Description
 * 搜狗新闻列表解析
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/news_list/spider_result/eee41fa74f7ec32b3cb24f5f1b7d1eca";
//$collect_download_url = "http://news.sogou.com/news?&clusterId=&p=42230305&query=%E7%BF%BC%E8%99%8E&mode=1&media=&sort=1&num=50&ie=utf8";
ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/sogou_news_log.txt'); //将出错信息输出到一个文本文件 

$html = file_get_contents($collect_download_url);
//file_put_contents('sogou-html', $html);
$final_result = array();
$html=iconv("GBK", "UTF-8//IGNORE", $html);
$main_reg = '/<div id="main" class="main">(.*)<\/div>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
$li_reg = '/<div class="vrwrap">(.*?)<\/div>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/<a [target="_blank"]* *href="(.*?)"/s';
    $title_reg = '/<h3 class="vrTitle">(.*?)<\/h3>/s';
    $abstract_reg = '/<span id="summary_[\d]+">(.*?)<\/span>/s';
    $from_reg = '/<p class="news-from">(.*?)&nbsp;/s';
    $time_reg = '/resultinfodate:(.*?)-->/s';

    preg_match($url_reg, $value[0], $url_result);

    if(empty($url_result)) {
        //print_r($value);
        continue;
    }

    preg_match($title_reg, $value[0], $title_result);
    preg_match($abstract_reg, $value[0], $abstract_result);
    preg_match($from_reg, $value[0], $from_result);
    preg_match($time_reg, $value[0], $time_result);
    if(empty($time_result)){
        $time_reg = '/&nbsp;(.*?)<\/p>/s';
        preg_match($time_reg, $value[0], $time_result);
    }

    // 处理时间
    $time_str = '';
    $pubtime = 0;
    if(!empty($time_result[1])) {
        $time_str = $time_result[1];
        $p = strpos($time_str, "分");
        $sec = 60;
        if(!$p) {
            $p = strpos($time_str, "小时");
            $sec = 3600;
        }
        if(!$p) {
            $p = strpos($time_str, '天');
            if($p !== false) {
                $sec = 86400;
            }
        }
        if($p > 0) {
            $dd = substr($time_str, 0, $p);
            $pubtime = time() - $dd * $sec;
            if($sec == 86400) {
                $pubtime = time() - 86400;
            }
        }else{

            $time_str = str_replace("年","-",$time_str);
            $time_str = str_replace("月","-",$time_str);
            $time_str = str_replace("日","",$time_str);
            $pubtime = strtotime($time_str);
        }
    }
    
    if(empty($time_result[1]) or empty($from_result[1]) or empty($title_result[1])) {
        //print_r($value);
		continue;
	}
	
    //$pubtime = strtotime($time_result[1]);
    $title = trim(strip_tags($title_result[1]));
    if(empty($abstract_result)){
        $abstract = trim(strip_tags($title_result[1])); 
    }else{
        $abstract = trim(strip_tags($abstract_result[1])); 
    }
    
    $from = $from_result[1];

	if($from === '看看新闻网') {
		continue;
	}
	
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => $title,
        'abstract' => $abstract,
        'from' => $from,
        'author' => $from,
        'source' => 1,
        'time' => $pubtime,
        'channel' => '搜狗新闻',
    );
    unset($pubtime);
    unset($title);
    unset($abstract);
    unset($from);
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('sogou-news-lists', var_export($final_result, true));
