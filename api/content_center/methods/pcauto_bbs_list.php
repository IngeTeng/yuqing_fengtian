<?php

/**
 * @filename pcauto_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-16  16:41:04
 * @updatetime 2016-8-16  16:41:04
 * @version 1.0
 * @Description
 * 太平洋汽车网论坛解析
 * 
 */


// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/bbs_list/spider_result/b3047079e09f0cf38ce1ed4446cec948";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="content">(.*)<\/div>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

$li_reg = '/<div class="paragraph">(.*?)<\/div>\s+<\/div>/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {
    $utf8_content = iconv('gbk', 'utf-8', $value[0]);

    $url_reg = '/<a href="([^"]+)"/s';
    $title_reg = '/<strong>(.*?)<\/strong>/s';        // 标题
    $content_reg = '/<div class="doc">(.*?)<\/div>/s';      // 内容

    $time_reg = '/<i>时间：<\/i>(.*?)<\/span>/s';       // 发布时间
    $author_reg = '/<i>作者：<\/i>(.*?)<\/span>/s';     // 作者
    $forum_reg = '/<i>论坛：<\/i>(.*?)<\/span>/s';

    preg_match($url_reg, $utf8_content, $url_result);
    preg_match($title_reg, $utf8_content, $title_result);
    preg_match($content_reg, $utf8_content, $content_result);
    preg_match($author_reg, $utf8_content, $author_result);
    preg_match($forum_reg, $utf8_content, $forum_result);
    preg_match($time_reg, $utf8_content, $time_result);

    $content = trim(strip_tags($content_result[1]));
	
	$url = $url_result[1];
	$p = strpos($url_result[1], '?');
	if($p !== false) {
		$url = substr($url_result[1], 0, $p);
	}

    $final_result[] = array(            
        'url' => $url,     
        'title' => trim(strip_tags($title_result[1])),    
        'time' => strtotime($time_result[1]),     // 文章发布时间
        'content' => $content,
        'summary' => $content,
        'reply' => 0,
        'click' => 0,
        'author' => trim(strip_tags($author_result[1])),
        'source'=>1,
        'forum' => trim(strip_tags($forum_result[1])),
        'media' => '太平洋汽车网',        
    );
    // 删除上一次的记录
    unset($author_result);
    unset($forum_result);
}

$response_result = $final_result;
file_put_contents('pacuto-bbs-list', var_export($final_result, true));