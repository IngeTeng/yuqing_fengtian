<?php

/**
 * @filename ifeng_bbs_content.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-18  14:39:22
 * @updatetime 2016-8-18  14:39:22
 * @version 1.0
 * @Description
 * 解析凤凰网论坛帖子内容
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = 'http://bbs.auto.ifeng.com/thread-2704960-1-1.html?_dsign=d1612544';

$content_html = file_get_contents($collect_download_url);

$content_reg = '/<div class="t_fsz">(.*?)<div id="comment_/s';
preg_match($content_reg, $content_html, $content_result);
if(empty($content_result)) {
    $content_html = file_get_contents($collect_download_url);
    preg_match($content_reg, $content_html, $content_result);
}
else {
    $content = trim(strip_tags($content_result[1]));        // 删除标签
    $content = preg_replace('/\s/', '', $content);      // 删除空格
}

if(mb_strlen($content, "utf8") >= 30) {
    $summary = mb_substr($content , 0, 30, "utf8");
}
else {
    $summary = $content;
}

$response_result = array('content' => $content, 'summary' => $summary);

