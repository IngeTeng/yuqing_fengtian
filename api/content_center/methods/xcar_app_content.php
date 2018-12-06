<?php

/**
 * @filename xcar_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  16:05:41
 * @updatetime 2016-8-15  16:05:41
 * @version 1.0
 * @Description
 * 爱卡汽车网内容解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];

$final_result = array();
$content_html = file_get_contents($collect_download_url);
$content_reg = '/<div class="p_con">(.*)<\/div>/s';
preg_match($content_reg, $content_html, $content_result);
$content = strip_tags($content_result[1]);
$content = preg_replace('/\s/', '', $content);

$final_result = array('content' => $content, 'url' => '');

$response_result = $final_result;
