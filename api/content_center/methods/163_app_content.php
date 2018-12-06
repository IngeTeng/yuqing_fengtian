<?php

/**
 * @filename 163_app_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-14  21:29:24
 * @updatetime 2016-8-14  21:29:24
 * @version 1.0
 * @Description
 * 网易内容解析
 * 
 */

$list_result = $post_obj['result'];
$final_result = array();

$content_url = "http://c.m.163.com/nc/article/{$list_result['id']}/full.html";
$content_json = file_get_contents($content_url);
$content_array = json_decode($content_json, true);

$content = strip_tags($content_array[$list_result['id']]['body']);
$content = preg_replace('/\s/', '', $content);

$final_result = array('url'=>'', 'content'=>$content);

$response_result = $final_result;


