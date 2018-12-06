<?php

/**
 * @filename bbs_article.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-7-31  19:28:54
 * @updatetime 2016-7-31  19:28:54
 * @version 1.0
 * @Description
 * 天涯bbs文章内容解析
 * 
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/bbs_article/spider_result/be1eec5cde46868db8378b79e707d599";

$html = file_get_contents($collect_download_url);
$final_result = array();

$title_reg = '/<span class="s_title">(.*?)<\/span>/s';        // 标题
$content_reg = '/<div class="bbs-content clearfix">(.*?)<\/div>/s';      // 内容
$summary_reg = '/<meta name="description"(.*?)content="(.*?)" \/>/s';       // 梗概
$reply_reg = '/<span title="[^"]+">回复：(.*?)<\/span>/s';      // 回复
$click_reg = '/<span>点击：(.*?)<\/span>/s';     // 点击
$author_reg = '/span>楼主：<a[^>]+>(.*?)<\/a>/s';     // 作者
$forum_wrap_reg = '/<p class="crumbs">(.*?)<\/p>/s';       // 论坛外层
$forum_reg1 = '/<a href="\/list[^"]+" rel="nofollow">(.*?)<\/a>/s';

preg_match($title_reg, $html, $title_result);
preg_match($content_reg, $html, $content_result);
preg_match($summary_reg, $html, $summary_result);
preg_match($reply_reg, $html, $reply_result);
preg_match($click_reg, $html, $click_result);
preg_match($author_reg, $html, $author_result);
preg_match($forum_wrap_reg, $html, $forum_wrap);
preg_match($forum_reg1, $forum_wrap[1], $forum_result);

if(empty($forum_result)) {
    $forum_reg2 = '/<a href="\/list[^"]+">(.*?)<\/a>/s';
    preg_match($forum_reg2, $forum_wrap[1], $forum_result);
}

$final_result = array(
    'url' => $post_obj['collect_url'],     // bbsURL
    'title' => trim(strip_tags($title_result[1])),        // 删除标签
    'time' => $post_obj['bbs_public_time'],     // 文章发布时间
    'content' => trim(strip_tags($content_result[1])),     // 删除标签和前后空格
    'summary' => $summary_result[2],
    'reply' => $reply_result[1],
    /******************/
    'source'=>1,
    /*******************/
    'click' => $click_result[1],
    'author' => $author_result[1],
    'forum' => $forum_result[1],
    'media' => '天涯论坛',
);

$response_result = $final_result;
