<?php

/**
 * @filename ifeng_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-17  16:44:50
 * @updatetime 2016-8-17  16:44:50
 * @version 1.0
 * @Description
 * 凤凰论坛文章列表解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = 'http://bbs.auto.ifeng.com/forum-1020316-1.html';
$final_result = array();
$html = file_get_contents($collect_download_url);

// 匹配论坛名称
$forum_reg = '/<h1 class="xs2">[^>]+>(.*?)<\/a>/s';
preg_match($forum_reg, $html, $forum_result);

// 匹配文章列表
$article_reg = '/<tbody id="normalthread_(.*?)<\/tbody>/s';
preg_match_all($article_reg, $html, $article_list, PREG_SET_ORDER);

foreach($article_list as $article) {
    $url_reg = '/<a href="(.*?)"/s';
    $title_reg = '/class="s xst">(.*?)<\/a>/s';
    $time_reg = '/<em><a href="[^>]+><span title="(.*?)"/s';
    
    preg_match($time_reg, $article[1], $time_result);
    
    if(empty($time_result)) {
        $time_reg = '/<em><a href="[^>]+>(.*?)<\/a>/s';
        preg_match($time_reg, $article[1], $time_result);
    }
    
    $time = strtotime($time_result[1]);
    // 筛选一天之内的文章
    if((time() - $time) > 86400) {
        continue;
    }
    
    $info_reg ='/<td class="num">(.*?)<\/td>/s';
    $reply_reg = '/class="xi2">(.*?)<\/a>/s';
    $click_reg = '/<em>(.*?)<\/em>/s';
    
    preg_match($url_reg, $article[1], $url_result);
    preg_match($title_reg, $article[1], $title_result);
    preg_match($info_reg, $article[1], $info_result);
    preg_match($reply_reg, $info_result[1], $reply_result);
    preg_match($click_reg, $info_result[1], $click_result);

    
    $final_result[] = array(
        'url' => $url_result[1],
        'title' => $title_result[1],
        'time' => $time,
        'forum' => $forum_result[1],
        'reply' => $reply_result[1],
        'click' => $click_result[1],
        'media' => '凤凰汽车论坛',
        'author' => '',
    );
}

$response_result = $final_result;