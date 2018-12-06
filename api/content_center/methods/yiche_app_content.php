<?php

/**
 * @filename yiche_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-13  11:17:50
 * @updatetime 2016-8-13  11:17:50
 * @version 1.0
 * @Description
 * 易车网内容解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];

$content_json = file_get_contents($collect_download_url);
$content_array = json_decode($content_json, true);
$final_resutl = array();

if(isset($content_array['Data']['content'])) {
    $content = trim(strip_tags(base64_decode($content_array['Data']['content'])));        // 删除标签
    $content = preg_replace('/\s/', '', $content);      // 删除空格
    $url = $content_array['Data']['filepath'];
    $final_resutl = array('content' => $content, 'url' => $url);
}

$response_result = $final_resutl;

