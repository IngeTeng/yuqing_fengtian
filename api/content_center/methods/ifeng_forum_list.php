<?php

/**
 * @filename pcauto_forum_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-17  16:48:56
 * @updatetime 2016-8-17  16:48:56
 * @version 1.0
 * @Description
 * 解析太平洋论坛列表
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];      // 获取凤凰网首页
//$collect_download_url = 'http://bbs.auto.ifeng.com/';
$final_result = array();
$header = get_headers($collect_download_url);
$cookie1 = $header[7];
$cookie2 = $header[8];
//print_r($header);
//$html = file_get_contents($collect_download_url);
$ch = curl_init();
 // 2. 设置选项，包括URL
 curl_setopt($ch,CURLOPT_URL, $collect_download_url);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch,CURLOPT_HEADER,$cookie1.' '.$cookie2); 
 // 3. 执行并获取HTML文档内容
 $html = curl_exec($ch);
//print_r($html);
preg_match_all('/href="([^"]+)"/s', $html, $forum_urls, PREG_SET_ORDER);
$temp_list = array();

// 获取论坛列表
foreach($forum_urls as $forum_url) {
    if(strpos($forum_url[1], 'http://bbs.auto.ifeng.com/forum-') !== false) {
        if(strpos($forum_url[1], 'gid') !== false) {
            continue;
        }
        if(isset($temp_list[$forum_url[1]])) {
            continue;
        }
        $final_result[] = $forum_url[1];
        $temp_list[$forum_url[1]] = 1;
    }
}
//print_r($final_result);
$response_result = $final_result;