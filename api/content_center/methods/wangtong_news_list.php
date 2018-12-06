<?php

/**
 * @filename wangtong_news_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-11  19:55:55
 * @updatetime 2016-11-11  19:55:55
 * @version 1.0
 * @Description
 * 网通社列表解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/news_list/spider_result/6711719512d0b912622a9a409bb7d532";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="ina_left_nr ina_message">(.*?)<div class="ina_page" id="show">/s';



// 最外层匹配
preg_match($main_reg, $html, $main_result);


// 懒惰匹配
$li_reg = '/<div class="ina_news">(.*?)<\/div>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);


foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/href="(.*?)"/s';

    $title_reg = '/<h2 class=\'ina_keyword_bt\'>(.*?)<\/h2>/s';
    $abstract_reg = '/<h3>(.*?)<\/h3>/s';
    $time_reg = '/<span class=\'ina_date\'>(.*?)<\/span>/s';

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($abstract_reg, $value[0], $abstract_result);
    preg_match($time_reg, $value[0], $time_result);



    //获取文章发布时间start
//    $proxy_file = '/home/wiipu/local/apache/htdocs/task_center/proxy/';
//    $proxy_str = file_get_contents('C:/wamp64/www/htdocs/yuqing2/task_center/proxy/'. date('Y_m_d'));
//    $response['proxy'] = json_decode($proxy_str, true);
    //print_r($response);
    //$task_array = json_decode($response, true);
//    $proxy_infos = array_merge(array(array('proxy' => '', 'port' => '')), $response['proxy']);
    //print_r($proxy_infos);
    // foreach($proxy_infos as $proxy_info) {
    //     if(empty($proxy_info['proxy'])){
    //         continue;
    //     }
    //    // print_r($proxy_info);
    //     $html = get_html($url_result[1], '', $proxy_info['proxy'], $proxy_info['port']);
    //     if(strlen($html) > 500)
    //         break;
    //         //print_r($html);
    // }
    // if(strlen($html) < 500){
    //     $html = file_get_contents($url_result[1]);
    // }
//    $context = stream_context_create(array(
//        'http' => array(
//            'timeout' => 60//超时时间，单位为秒
//        )
//    ));
//    $html = file_get_contents($url_result[1]);
//    preg_match($time_reg, $html, $time_result);
    //获取文章发布时间end
    //print_r($html);
    //break;
    //print_r($time_result);
    //print_r($title_result);
    //print_r($url_result);
//    if(empty($time_result[1]) or empty($title_result[1]) or empty($url_result[1])) {
//        continue;
//    }

//    $time_str = $time_result[1];
//    $time_str = str_replace("年;", "-",$time_str);
//    $time_str = str_replace("月;", "-",$time_str);
//    $time_str = str_replace("日;", "",$time_str);
//    $time_str = str_replace("&#24180;", "-",$time_str);
//    $time_str = str_replace("&#26376;", "-",$time_str);
//    $time_str = str_replace("&#26085;", "",$time_str);

    $final_result[] = array(
        'url' => $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'abstract' => trim(strip_tags($abstract_result[1])),
        'from' => '网通社',
        'author' => "",
        'source' => 1,
        'time' => strtotime($time_result[0]),
        'channel' => '网通社',
    );

}
$response_result = $final_result;
file_put_contents('wangtong-news-lists', var_export($final_result, true));

