<?php

/**
 * @filename ifeng_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-13  14:27:45
 * @updatetime 2016-8-13  14:27:45
 * @version 1.0
 * @Description
 * 凤凰新闻内容解析
 */

$collect_download_url = $post_obj['collect_download_url'];

$final_result = array();
$content_json = file_get_contents($collect_download_url);
$content_array = json_decode($content_json, true);

if(isset($content_array['body']['text'])) {
    $content = trim(strip_tags($content_array['body']['text']));        // 删除标签
    $content = preg_replace('/\s/', '', $content);      // 删除空格
    $url = isset($content_array['body']['wapurl']) ? $content_array['body']['wapurl']:$content_array['body']['wwwurl'];
    $final_result = array('content' => $content, 'url' => $url);
}

$response_result = $final_result;
