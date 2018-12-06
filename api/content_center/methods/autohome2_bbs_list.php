<?php

/**
 * @filename autohome2_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl
 * @createtime 2018-03-08 
 * @updatetime 2018-03-08
 * @version 1.0
 * @Description
 * 抓包发现的汽车之家论坛搜索2 API解析
 * 
 */


// ini_set('display_errors',1);            //错误信息  
// ini_set('display_startup_errors',1);    //php启动错误信息  
 //error_reporting(0);                    //打印出所有的 错误信息  
// ini_set('error_log', dirname(__FILE__) . '/autohome2_bbs_log.txt'); //将出错信息输出到一个文本文件 
// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/bbs_list/spider_result/16d5ba8f09ddba73f75d72e65dad5a2f";
//$collect_download_url = "http://sou2.api.autohome.com.cn/wrap/v3/topic/search?_appid=app&_callback=jsonp_5_116&class=&ignores=content&modify=0&offset=0&pf=h5&q=%E4%B8%B0%E7%94%B0&range=&s=1&size=100&sort=new&tm=app";
$html = file_get_contents($collect_download_url);
$final_result = array();

//$html=iconv("GBK", "UTF-8//IGNORE", $html);
$main_reg = '/({(.*)})/s';
//file_put_contents('autohome-bbs-html', $html);
// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result[1]);
$json_array = json_decode($main_result[0], true);
//print_r($json_array);

$li_result = $json_array['result']['hitlist'];

foreach($li_result as $value) {

    //print_r($value);
    $final_result[] = array(            
        'url' => $value['data']['url'],     
        'title' =>  $value['data']['title'],        // 删除标签
        'time' =>  strtotime($value['data']['date']),     // 文章发布时间
        'content' => $value['light']['content'],
        'summary' => $value['light']['content'],
        'reply' => 0,
        'click' => 0,
        'author' => $value['data']['author'],
        'source'=>1,//
        'forum' => '',
        'media' => '汽车之家',
    );
    // 删除上一次的记录
    unset($author_result);
    unset($forum_result);
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('autohome2-bbs-list', var_export($final_result, true));

