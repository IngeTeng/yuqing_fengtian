<?php

/**
 * @filename pcauto_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  15:35:36
 * @updatetime 2016-8-15  15:35:36
 * @version 1.0
 * @Description
 * 太平洋汽车网api内容解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];

$final_result = array();
$content_html = file_get_contents($collect_download_url);
$content_reg = '/<div class="content" id="changeFontSizeWrap">(.*?)<script/s';
preg_match($content_reg, $content_html, $content_result);
$content = strip_tags($content_result[1]);
$content = preg_replace('/\s/', '', $content);

$final_result = array('content' => $content, 'url' => '');

$response_result = $final_result;
