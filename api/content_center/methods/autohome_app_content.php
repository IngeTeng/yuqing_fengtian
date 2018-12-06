<?php

/**
 * @filename autohome_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-14  18:11:33
 * @updatetime 2016-8-14  18:11:33
 * @version 1.0
 * @Description
 * 汽车之家内容解析
 * 
 */
ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/autohome_appcont_log.txt'); //将出错信息输出到一个文本文件 

$list_result = $post_obj['result'];
$final_result = array();

if($list_result['mediatype'] == 0 or $list_result['mediatype'] == 1) {
    // 普通文章
    //$content_url = "http://cont.app.autohome.com.cn/autov4.3/content/news/newscontent-a2-pm2-v4.3.0-n{$list_result['id']}-lz0-sp0-nt0-sa1-p1-c1-fs0-cw360.html";
    $content_url = "http://cont.app.autohome.com.cn/cont_v8.5.0/content/news/newscontent-pm2-n{$list_result['id']}-t0-rct0-ish1-ver20180124130740.json";
    $content_html = file_get_contents($content_url);
    
   // $time_reg = '/<span class="aricle-info-date">(.*?)<\/span>/s';
    $content_reg = '/<div class="aricle-content">(.*)<\/div>/s';
    
    //preg_match($time_reg, $content_html, $time_result);
    preg_match($content_reg, $content_html, $content_result);
    $content = trim(strip_tags($content_result[1]));        // 删除标签
    $content = preg_replace('/\s/', '', $content);      // 删除空格
    
    $final_result = array('url'=>$content_url, 'content'=>$content);
}
// elseif($list_result['mediatype'] == 26) {
//     // 话题
//     $content_url = "https://cont.app.autohome.com.cn/cont_v8.5.0/news/topicfinalpage.aspx?pm=2&topicid=449&ish=1&ver=1";
//     $content_html = file_get_contents($content_url);
    
//     $time_reg = '/<span class="shuoke-info-date">(.*?)<\/span>/s';
//     $content_reg = '/<div class="shuoke-content"><div>(.*)<div class="articleCopy">/s';
    
//     preg_match($time_reg, $content_html, $time_result);
//     preg_match($content_reg, $content_html, $content_result);
//     $content = trim(strip_tags($content_result[1]));        // 删除标签
//     $content = preg_replace('/\s/', '', $content);      // 删除空格
    
//     $final_result = array('url'=>$content_url, 'time'=>strtotime($time_result[1]), 'content'=>$content);
// }
elseif($list_result['mediatype'] == 2) {//图说
    // 视频
    $content_url   = "http://cont.app.autohome.com.cn/cont_v8.5.0/news/newsdetailpicarticle-pm2-nid{$list_result['id']}-t0-isvr1.json";
    $content_json  = file_get_contents($content_url);
    $content_array = json_decode($content_json, true);
    $content_url2  = $result['shareurl'];
    $content_html  = file_get_contents($content_url2);
    $time_reg = '/<span class="time">(.*?)<\/span>/s';
    preg_match($time_reg, $content_html, $time_result);
    $time = '';
    if(!empty($time_result[1])){
        $time = strtotime($time_result[1]);
    }
    
    if(!empty($content_array['result'])) {
        $result = $content_array['result'];
        $final_result = array(
            'url'=>$result['shareurl'],  
            'content'=>strip_tags($result['image'][0]['description']),
            'time'  => empty($time)?$list_result['time']:$time,
            );
    }
}

$response_result = $final_result;
//file_put_contents('auto-appcont', var_export($final_result, true));