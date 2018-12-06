<?php

/**
 * @filename autohome_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  21:48:44
 * @updatetime 2016-8-15  21:48:44
 * @version 1.0
 * @Description
 * 汽车之家论坛搜索列表解析
 * 
 */


ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/autohome-all_bbs_log.txt'); //将出错信息输出到一个文本文件 
// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "5e1446f48639165c6570f06af54beaa8";
//$collect_download_url = "http://sou.autohome.com.cn/luntan?q=%BF%AD%C3%C0%C8%F0&pvareaid=100834&entry=44&clubClassBefore=0&IsSelect=0&clubOrder=New&clubClass=0&clubSearchType=&clubSearchTime=&pq=%25s&pt=636312223671779902";
$html = file_get_contents($collect_download_url);
$final_result = array();
//print_r($html);
$html=iconv("GBK", "UTF-8//IGNORE", $html);
$main_reg = '/<div class="carea"(.*?)<div class="pagearea"/s';
file_put_contents('autohome-bbs-html', $html);
// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
// 懒惰匹配    
$li_reg = '/<dl class="list_dl"(.*?)<\/dl>/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach($li_result as $value) {
    //print_r($value);
    $url_reg = '/target="_blank" href="([^"]+)"/s';
    $title_reg = '/<dt>(.*?)<\/dt>/s';        // 标题
    $author_reg = '/class="linkblack">(.*?)<\/a><span class="tdate"/s';
    $time_reg1 = '/<span class="tdate">(.*?)<\/span>/s';
    preg_match($time_reg1, $value[0], $time_result1);
    $time_reg2 = "/$time_result1[1](.*?)\|/s";
    preg_match($time_reg2, $value[0], $time_result2);
    preg_match($author_reg, $value[0], $author_result);
    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);

    $time = $time_result1[1].$time_result2[1];
    //print_r($time);
    //print_r($author_result);

    // $content = iconv("gbk", "utf-8", trim(strip_tags($content_result[1])));
    $title = trim(strip_tags($title_result[1]));
	//print_r($title);
	$url = 'club.autohome.com.cn'.$url_result[1];
	//print_r($url);
	
    $final_result[] = array(            
        'url' => $url,     
        'title' => $title,        // 删除标签
        'time' => strtotime($time),     // 文章发布时间
        'content' => $title,
        'summary' => $title,
        'reply' => 0,
        'click' => 0,
        'author' => $author_result[1],
        'forum' => '',
        'media' => '汽车之家车型论坛',
    );
    // 删除上一次的记录
    unset($author_result);
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('autohome-all-bbs-res', var_export($final_result, true));

