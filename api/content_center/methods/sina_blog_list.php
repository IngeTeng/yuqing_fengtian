<?php

/**
 * @filename sina_blog_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-27  11:00:59
 * @updatetime 2016-8-27  11:00:59
 * @version 1.0
 * @Description
 * 新浪博客解析
 * 
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing2/wii_spider/blog_list/spider_result/b2039572090ddc8b4a5af4c0acda0476";
//$collect_download_url = "http://spiderzhongdianfengtian.test.com/2fd9f6628dbcff13b89ce75c9512a03b";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div id="result"(.*)<div class="sRight">/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/<div class="box-result clearfix"(.*?)<\/div>\s+<\/div>/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {
    $li_value = iconv('gbk', 'utf-8', $value[0]);
    
    $url_reg = '/<a href="([^"]+)"/s';
    $title_reg = '/<a[^>]+>(.*?)<\/a>/s';
    $content_reg = '/<p class="content">(.*?)<\/p>/s';      // 内容
    $time_reg = '/<span class="fgray_time">(.*?)<\/span>/';       // 发布时间
    $author_reg = "/class='rib-author'>(.*?)<\/a>/s";     // 作者

    preg_match($url_reg, $li_value, $url_result);
    preg_match($title_reg, $li_value, $title_result);
    preg_match($content_reg, $li_value, $content_result);
    preg_match($time_reg, $li_value, $time_result);
    preg_match($author_reg, $li_value, $author_result);

    $content = trim(strip_tags($content_result[1]));
    $final_result[] = array(            
        'url' => $url_result[1],     
        'title' => trim(strip_tags($title_result[1])),        // 删除标签
        'time' => strtotime($time_result[1]),     // 文章发布时间
        'content' => $content,
        'summary' => $content,
        'source'=>1,
        'author' => $author_result[1],
        'media' => '新浪博客',        
    );
    unset($forum_result);
}
//print_r($final_result);
$response_result = $final_result;