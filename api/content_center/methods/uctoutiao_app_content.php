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
//$collect_download_url = "http://m.uczzd.cn/ucnews/news?app=ucnews-iflow&aid=14499411522402077884";
$final_result = array();

$content_html = file_get_contents($collect_download_url);
$content_reg = '/"content":"(.*)<\/p>",/s';
preg_match($content_reg, $content_html, $content_result);
//print_r($content_html);
// if(empty($content_result)){
// 	$content_reg = '/<div class="article-content(.*)<\/p><\/div>/s';
// 	preg_match($content_reg, $content_html, $content_result);
// }

//if(empty($content_result)){
//	continue;
//}
$content = strip_tags($content_result[1]);

//$content = strip_tags($content_array[$list_result['id']]['body']);
$content = preg_replace('/\s/', '', $content);

$final_result = array('url'=>'', 'content'=>$content);

$response_result = $final_result;
//file_put_contents('uctoutiao', var_export($final_result, true));
//print_r($final_result);

