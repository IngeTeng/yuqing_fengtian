<?php

/**
 * @filename zaker_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  16:55:44
 * @updatetime 2016-8-15  16:55:44
 * @version 1.0
 * @Description
 * zaker内容解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];

$final_result = array();
$the_response = file_get_contents($collect_download_url);
@$content_json = gzdecode($the_response);

if(!empty($content_json)) {
    $content_array = json_decode($content_json, true);
    if($content_array['stat'] == 1) {
        $content_reg = '/<div id="content">(.*)<\/div>/s';
        preg_match($content_reg, $content_array['data']['content'], $content_result);
        $content = strip_tags($content_result[1]);
        $content = preg_replace('/\s/', '', $content);
        
        $final_result = array('content' => $content, 'url' => '');
    }
}

$response_result = $final_result;


