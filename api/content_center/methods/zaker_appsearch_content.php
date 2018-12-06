<?php

/**
 * @filename zaker_appsearch_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-10  22:02:14
 * @updatetime 2016-11-10  22:02:14
 * @version 1.0
 * @Description
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = 'http://www.myzaker.com/article/58242f379490cb0360000060/';

$final_result = array();
$html = file_get_contents($collect_download_url);

$content_reg = '/<div id="content">(.*?)<\/div>/s';
preg_match($content_reg, $html, $content_result);
$content = strip_tags($content_result[1]);
$content = preg_replace('/\s/', '', $content);
        
$final_result = array('content' => $content);

$response_result = $final_result;
file_put_contents('zaker-apps-content', var_export($response_result));
