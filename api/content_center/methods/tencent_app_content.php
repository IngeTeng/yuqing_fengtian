<?php

/**
 * @filename tencent_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  14:59:38
 * @updatetime 2016-8-15  14:59:38
 * @version 1.0
 * @Description
 * 腾讯api内容解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];

$final_result = array();
$content_json = file_get_contents($collect_download_url);
$content_array = json_decode($content_json, true);

if($content_array['ret'] === 0) {
    $content = trim(strip_tags($content_array['content']['text']));        // 删除标签
    $content = preg_replace('/\s/', '', $content);      // 删除空格
    $final_result = array('content' => $content, 'url' => $content_array['url']);
}

$response_result = $final_result;
