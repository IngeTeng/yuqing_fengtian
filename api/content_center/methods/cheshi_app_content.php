<?php

/**
 * @filename cheshi_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-10  16:11:53
 * @updatetime 2016-8-10  16:11:53
 * @version 1.0
 * @Description
 * 解析车市文章内容
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];

$content_html = file_get_contents($collect_download_url);
$content_reg = '/<p>(.*?)<span class="cs_allword">展开全文<\/span>/s';
preg_match($content_reg, $content_html, $content_result);
$content = trim(strip_tags($content_result[1]));        // 删除标签
$content = preg_replace('/\s/', '', $content);      // 删除空格

$response_result = array('content' => $content, 'url' => '');


