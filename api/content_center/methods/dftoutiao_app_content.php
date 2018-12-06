<?php

/**
 * @filename uctoutiao_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2017-12-20  11:29:24
 * @updatetime 
 * @version 1.0
 * @Description
 * uc头条内容解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "https://mini.eastday.com/mobile/180129083009471.html";
$final_result = array();

$content_html = file_get_contents($collect_download_url);
$content_reg = '/<div id="content" class="J-article-content article-content">(.*)<\/article>/s';
preg_match($content_reg, $content_html, $content_result);
//print_r($content_html);

$time_reg = '/<span class="src">(.*)&nbsp;&nbsp;/s';
preg_match($time_reg, $content_html, $time_result);



$content = strip_tags($content_result[1]);

//$content = strip_tags($content_array[$list_result['id']]['body']);
$content = preg_replace('/\s/', '', $content);

$final_result = array(
	'url'=>'', 
	'time' => empty($post_obj['result']['time'])?'':$post_obj['result']['time'],
	'content'=>$content
	);

$response_result = $final_result;
file_put_contents('df-appcontent', var_export($final_result, true));
//print_r($final_result);

