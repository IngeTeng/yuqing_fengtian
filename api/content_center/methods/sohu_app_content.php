<?php

/**
 * @filename sohu_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-13  20:19:36
 * @updatetime 2016-8-13  20:19:36
 * @version 1.0
 * @Description
 * 搜狐内容解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
$content_html = file_get_contents($collect_download_url);

$content_reg = '/<abstract>(.*?)<\/abstract>/s';
$url_reg = '/http:\/\/3g.k.sohu.com(.*?)\s/s';

preg_match($content_reg, $content_html, $content_result);
preg_match($url_reg, $content_html, $url_result);

if(empty($content_result[1])) {
    $content_reg2 = '/<content><!\[CDATA\[(.*?)\]\]><\/content>/s';
    preg_match($content_reg2, $content_html, $content_result);
}
$content = trim(strip_tags($content_result[1]));        // 删除标签
$content = preg_replace('/\s/', '', $content);      // 删除空格
$content = preg_replace('/a{.*}/s', '', $content);

$response_result = array('content' => $content, 'url' => $url_result[0]);
