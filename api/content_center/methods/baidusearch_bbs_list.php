<?php

/**
 * @filename baidusearch_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2018-01-19
 * @updatetime 
 * @version 1.0
 * @Description
 * 百度搜索论坛解析
 * 
 */

function getRealURL($url) { // 获取真实的url
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);    //表示需要response header
    curl_setopt($ch, CURLOPT_NOBODY, true); //表示需要response body
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    //print_r(get_headers($url));
    // $header = get_headers($url);
    // $url_reg = '/Location: (.*)/s';
    // $match_url = preg_match($url_reg, $header[5], $real_url);
    //print_r($real_url);
    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
        return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    }
    
    return false;
}
error_reporting(0);
$collect_download_url = 'http://test.test.com/aaa.html';
//$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&rsv_idx=1&tn=baidu&wd=intitle:%E6%B1%89%E5%85%B0%E8%BE%BE+bbs&pn=0&rn=20&rsv_enter=1&gpc=stf%3D1516265657%2C1516352057%7Cstftype%3D1&tfflag=1";
//$collect_download_url = 'http://localhost/yuqing2/wii_spider/news_list/spider_result/6d412603915dd614c7fd0922385276a5';

$html = file_get_contents($collect_download_url);
//print_r($html);
$main_reg = '/<div id="content_left">(.*)<div style="clear:both;/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
$final_result = array();

$li_reg = '/<div class="result c-container(.*?)百度快照/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/href="(.*?)"/s';
    $title_reg = '/<h3 class="t">(.*?)<\/h3>/s';
    $abstract_reg = '/&nbsp;-&nbsp;<\/span>(.*?)<\/div>/s';
    $time_reg = '/<span class=" newTimeFactor_before_abs m">(.*?)&nbsp;-&nbsp;/s';

    preg_match($title_reg, $value[0], $title_result);
    preg_match($url_reg, $value[0], $url_result);
    preg_match($abstract_reg, $value[0], $abstract_result);
    preg_match($time_reg, $value[0], $time_result);
    if(empty($time_result)){//知道匹配不上
        $time_reg = '/最新回答: (.*?)<\/p>/s';
        preg_match($time_reg, $value[0], $time_result);
        //print_r($time_result);
    }
    
//     print_r($title_result);
     //print_r($url_result);
    // print_r($abstract_result);
   // print_r($time_result);
    
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
    //print_r($pubtime);
    if(($pubtime == 0) or empty($title_result[1]) or empty($url_result[1])) {
       // echo "1111\n";
        continue;
    }
    
    $abstract = trim(strip_tags($abstract_result[1]));
    $pp = strpos($abstract, '&nbsp;-&nbsp;');
    if($pp !== false) {
        $abstract = substr($abstract, $pp+13);
    }
    if(($url = getRealURL($url_result[1])) === false) {//获取真实的url
       
        if(get_headers($url_result[1],1) !=FALSE){
            $header = get_headers($url_result[1],1);
            $url = $header['Location'];
        }
        else{
            $url = $url_result[1];
        }

        //print_r($header);
        //echo '-2-';
        //continue;
    }
    $from = '';
    $title = trim(strip_tags($title_result[1]));
    $abstract = trim(strip_tags($abstract_result[1]));
    if(strpos($title, '汽车之家') or strpos($abstract, '汽车之家') ){
        $from = '汽车之家';
    }else{
        //$media_reg = '/:\\\\(.*?)\//s';
        $from = ' ';
    }
    
    $final_result[] = array(
        'url' => $url,
        'title' => $title,
        'content' => $abstract,
        'summary' => $abstract,
        'author' => "",
        'source'=>1,//
        'abstract' => $abstract,
        'media' => $from,
        'time' => $pubtime,
        'channel' => '百度网页搜索',
    );
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('baidusearch-bbs-list', var_export($final_result, true));