<?php

/**
 * @filename baidu_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-4  11:53:27
 * @updatetime 2016-8-4  11:53:27
 * @version 1.0
 * @Description
 * 解析百度贴吧列表
 * 
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/bbs_list/spider_result/e481a4b9f7a8b13def6c9261ef1b055f";
//$collect_download_url = "http://tieba.baidu.com/f/search/res?isnew=1&kw=&qw=%BF%AD%C3%C0%C8%F0&rn=30&un=&only_thread=0&sm=1&sd=&ed=&pn=1";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="s_post_list">(.*)<\/div>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/<div class="s_post">(.*?)<\/font>\s+<\/div>/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
unset($li_result[0]);

foreach($li_result as $value) {

    // 判断帖子是否删除
    if(strpos($value[0], '<span style="color:#9c9c9c;">') > 0) {
        $time_reg = '/class="p_green">(.*?)<\/font>/';       // 发布时间
        $content = '';
    }
    else {
        $time_reg = '/class="p_green p_date">(.*?)<\/font>/';       // 发布时间
        $author_reg = '/<font class="p_violet">(.*?)<font class="p_violet">(.*?)<\/font>/s';     // 作者
        $forum_reg = '/<font class="p_violet">(.*?)<\/font>/s';
        $content_reg = '/<div class="p_content">(.*?)<\/div>/s';      // 内容

        preg_match($content_reg, $value[0], $content_result);
        preg_match($author_reg, $value[0], $author_result);
        preg_match($forum_reg, $value[0], $forum_result);

        $content = iconv("gbk", "utf-8", trim(strip_tags($content_result[1])));
    }

    $url_reg = '/class="bluelink" href="(.*?)"/s';
    $title_reg = '/<span class="p_title">(.*?)<\/span>/s';        // 标题

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($time_reg, $value[0], $time_result);

    $title = iconv("gbk", "utf-8", trim(strip_tags($title_result[1])));

    // 处理回复
    if(strncmp($title, '回复:', strlen('回复:')) == 0) {
        $title = substr($title, strlen('回复:'));
    }
    if(strncmp($content, '回复', strlen('回复')) == 0) {
        $pos = strpos($content, ':');
        if($pos > 0) {
            $content = substr($content, $pos+1);
        }
    }
    
    if(empty($content)) {
        continue;
    }
    //print_r($url_result);
	$url = "http://tieba.baidu.com{$url_result[1]}";
	$p = strpos($url, '?');
	// if($p > 0) {
	// 	$url = substr($url, 0, $p);
	// }
    date_default_timezone_set('PRC');
 //    print_r($time_result);
	// $time = strtotime($time_result[1]);
 //    echo $time.'<br/>';
    $final_result[] = array(            
        'url' => $url,     
        'title' => $title,        // 删除标签
        'time' => strtotime($time_result[1]),     // 文章发布时间
        'content' => $content,
        'summary' => $content,
        'reply' => 0,
        'click' => 0,
        'source'=>1,
        'author' => empty($author_result[2])?'':iconv('gbk', 'utf-8', $author_result[2]),
        'forum' => empty($forum_result[1])?'':iconv("gbk", "utf-8", $forum_result[1]),
        'media' => '百度贴吧',        
    );
    // 删除上一次的记录
    unset($author_result);
    unset($forum_result);
}
//print_r($final_result);

$response_result = $final_result;
file_put_contents("baidu_bbs_list", var_export($final_result,TRUE));