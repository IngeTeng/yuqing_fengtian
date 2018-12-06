<?php

/**
 * @filename autohome_dealer_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2016-8-15  21:48:44
 * @updatetime 2016-8-15  21:48:44
 * @version 1.0
 * @Description
 * 汽车之家经销商报价解析
 * 
 */


ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/autohome_dealer_log.txt'); //将出错信息输出到一个文本文件 
// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/bbs_list/spider_result/16d5ba8f09ddba73f75d72e65dad5a2f";
//$collect_download_url = "7afbeacdc273e2053c31d46c8dc22ff0";
$html = file_get_contents($collect_download_url);
$html = str_replace('}{', ',', $html);
$html = iconv('gbk', 'utf-8', $html);
//print_r($html);

 //获取促销列表
$main_reg = '/<div class="dealeron-cont"(.*)<\/div>/s';
// 最外层匹配
preg_match($main_reg, $html, $main_result);
// 懒惰匹配    
$li_reg = '/<dl class="promot-dl "(.*?)<\/dl>/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//exit();
$final_result = array();

$dealer_reg = '/"Company":"(.*?)"/s';
preg_match($dealer_reg, $html, $dealer_result);
//print_r($dealer_result);
$dealer_name = $dealer_result[1];

foreach($li_result as $value) {
    //print_r($value);
    $url_reg = '/<a target="_blank" href="([^"]+)"/s';
    $title_reg = '/<p class="name font-yh">(.*?)<\/p>/s';        // 标题
    $content_reg = '/<p class="text">(.*?)<\/p>/s';//摘要

     //$str1 = iconv('utf-8', 'gb2312', '<p>注：');
    // $str2 = iconv('utf-8', 'gb2312', '行情');
    $time_reg = "/发布时间：(.*?)<\/span/s";

    preg_match($url_reg, $value[0], $url_result);
    //print_r($url_result);
    if(empty($url_result[1])){
        continue;
    }
    preg_match($title_reg, $value[0], $title_result);
    //print_r($title_result);
    preg_match($content_reg, $value[0], $content_result);
    //print_r($price_result);
    preg_match($time_reg, $value[0], $time_result);
    //print_r($time_result);
    $title_result[1] = trim(strip_tags($title_result[1]));

    $content = trim(strip_tags($content_result[1]));
    $title = trim(strip_tags($dealer_name.'-'.$title_result[1] ));
	$url  = 'http://dealer.autohome.com.cn'.$url_result[1];
    //print_r($value[0]);
    $time_str = $time_result[1];
    // $time_str = str_replace("年","-",$time_str);
    // $time_str = str_replace("月","-",$time_str);
    // $time_str = str_replace("日","",$time_str);
    $time_str .= ' 08:00:00';
    //print_r($time_str);
    $pubtime = strtotime($time_str);



	
    $final_result[] = array(            
        'url' => $url,     
        'title' => $title,        // 删除标签
        'time' => $pubtime,     // 文章发布时间
        'content' => $content,
        'summary' => $content,
        'abstract' => $content,
        'channel' => '汽车之家经销商',
        'media' => '汽车之家经销商',
    );
    // 删除上一次的记录
    unset($author_result);
    unset($forum_result);
}
//print_r($final_result);
$response_result = $final_result;
 //file_put_contents('autohome-bbs-res', var_export($final_result, true));

