<?php

/**
 * @filename sohutousu_news_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-10-11  23:38:48
 * @updatetime 2016-10-11  23:38:48
 * @version 1.0
 * @Description
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing2/wii_spider/news_list/spider_result/9387a3297ed09df39e2e653336c82efa";
$html = file_get_contents($collect_download_url);
$html = iconv('gbk', 'utf-8', $html);
$final_result = array();

$main_reg = '/<div class="cont_list">(.*?)<div class="pagelist">/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/<dl class="conts">(.*?)<\/dl>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {
//    print_r($value);
    
    $content_reg = '/<dd>(.*?)<\/dd>/s';
    preg_match($content_reg, $value[0], $content_result);
//    print_r($content_result);
    
    // 构建正则表达式
    $url_reg = '/<p>\s+<a target="_blank" href="(.*?)"/s';
    $title_reg = '/title="(.*?)"/s';
    $abstract_reg = '/<p>\s+<a[^>]+>(.*?)<\/a>/s';
    $time_reg = '/<span class="info">(.*?)&nbsp;&nbsp;&nbsp;&nbsp;/s';

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($abstract_reg, $value[0], $abstract_result);
    preg_match($time_reg, $value[0], $time_result);
    
    $time_str = str_replace("月", "-", $time_result[1]);
    $time_str = str_replace("日", "", $time_str);
    $time_str = trim(str_replace("    ", " ", $time_str));
    //print_r($time_result);
    //print_r($time_str);
    //echo '----------';
    $month    = substr($time_str, 0,2);
   // print_r($month);
    $nowmonth = date("m",time());//取得当前月份
    //echo 'now:'.$nowmonth;
    $diff = $nowmonth-$month;//计算月份差
    //echo "--diff:".$diff;
    if($diff > 1 || ($diff > -11 && $diff < 0)){//相差月份大于1个月，跳过
        //echo "跳过";
        continue;
    }
    
    $time_str = date("Y",time()) . '-'. $time_str;
    
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'abstract' => trim(strip_tags($abstract_result[1])),
        'from' => '搜狐汽车投诉',
        'time' => strtotime($time_str),
        'channel' => '搜狐汽车投诉',
    );
}
//print_r($final_result);
$response_result = $final_result;