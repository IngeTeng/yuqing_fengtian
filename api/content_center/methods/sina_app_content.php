<?php

/**
 * @filename sina_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-14  16:38:36
 * @updatetime 2016-8-14  16:38:36
 * @version 1.0
 * @Description
 * 解析新浪api内容
 */

$collect_download_url = $post_obj['collect_download_url'];

$final_result = array();
$content_json = file_get_contents($collect_download_url);
$content_array = json_decode($content_json, true);

if($content_array['status'] === 0) {
    $content = trim(strip_tags($content_array['data']['content']));        // 删除标签
    $content = preg_replace('/\s/', '', $content);      // 删除空格
    $url = isset($content_array['data']['link']) ? $content_array['data']['link']:'';
    $final_result = array('content' => $content, 'url' => $url);
}

$response_result = $final_result;
