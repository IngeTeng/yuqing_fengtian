<?php

/**
 * @filename autohome_bbs_list.php
 * @encoding UTF-8
 * @author WiiPu CzRzChao
 * @createtime 2016-8-15  21:48:44
 * @updatetime 2016-8-15  21:48:44
 * @version 1.0
 * @Description
 * 汽车之家说客搜索列表解析
 *
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];

//$collect_download_url = "http://127.0.0.1:8888/yuqing_fengtian/zhuaqu/spider_center/wii_spider1/blog_list/spider_result/90675200f2c3682f36ebc36a478c0184";
//$collect_download_url = "http://localhost/yuqing2/wii_spider/blog_list/spider_result/f5d671ac1ac11a0fc2fca78610ce386e";
// 获取缓存好的html
//$collect_download_url = "http://sou.autohome.com.cn/zonghe?q=%b9%e3%c6%fb%b7%e1%cc%ef&class=0&sort=New&pvareaid=100834&entry=42&error=0";
$html = file_get_contents($collect_download_url);
$html = iconv("gb2312", "utf-8//IGNORE", $html);
$final_result = array();
//print_r($html);
$main_reg = '/<div class="result-list">\s+(<dl.*)<\/div>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/<dl class="list-dl">(.*?)<\/dl>/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach ($li_result as $value) {
//    var_dump($value[0]);

    $url_reg = '/<a href="([^"]+)"/s';
    $title_reg = '/<dt>(.*?)<\/dt>/s';        // 标题
    $content_reg = '/<p(.*?)<\/p>/s';      // 内容
    $info_reg = '/<div class="div2">(.*?)<\/div>/s';

    $author_reg = '/<span>(.*?)<\/span><span/s';
    $article_click_reg = '/浏览数：(.*?)<\/span>/s';

    preg_match($info_reg, $value[0], $blog_info);
    //print_r($blog_info);
    if (empty($blog_info)) {
        //echo '---';
        continue;
    }
    //print_r($value);
    $time_reg = "/<span(.*?)<\/span>/s";

    preg_match($content_reg, $value[0], $content_result);
    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($author_reg, $value[0], $author_result);
    preg_match($time_reg, $blog_info[0], $time_result);
    preg_match($article_click_reg, $value[0], $article_click_result);
    $author = trim($author_result[1]);
    $article_click = trim($article_click_result[1]);
    //print_r($title_result);
    $content = trim(strip_tags($content_result[0]));
    $title = trim(strip_tags($title_result[1]));

    $url = $url_result[1];
    $p = strpos($url_result[1], '?');
    if ($p > 0) {
        $url = substr($url_result[1], 0, $p);
    }
    //过滤活动报名信息页面
    if (strpos($url_result[1], 'PartyRegisterList')) {
        continue;
    }
    $time = trim(strip_tags($time_result[0]));
    //print_r($time);
    //print_r(strtotime('2017-3-23'));
    $final_result[] = array(
        'url' => $url,
        'title' => $title,        // 删除标签
        'time' => strtotime($time),     // 文章发布时间
        'content' => $content,
        'summary' => $content,
        /**************/

        'click' => $article_click,
        'source' => 1,
        /******************/
        'author' => $author,
        'media' => '汽车之家说客',
    );
    // 删除上一次的记录
    unset($author_result);
    unset($forum_result);
}
//print_r($final_result);
$response_result = $final_result;
//file_put_contents('autohome_blob-list', var_export($final_result, true));