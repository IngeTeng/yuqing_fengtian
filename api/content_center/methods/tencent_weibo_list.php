<?php

/**
 * @filename weibo_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-7-21  11:21:59
 * @updatetime 2016-7-21  11:21:59
 * @version 1.0
 * @Description
 * 解析腾讯微博
 * 
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = 'http://localhost/yuqing/wii_spider/weibo_list/spider_result/df40f23fd4b317fd7eb726a80f28f7b6';

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<ul id="talkList" class="LC">(.*?)<\/ul>/s';
preg_match($main_reg, $html, $main_result);

$li_reg = '/<li\s*id="(.*?)<\/li>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $li) {
    $mid_reg = '/<a href="http:\/\/t.qq.com\/p\/t\/(\d*)"/s';     // mid
    $uname_reg = '/ gender="他"[^>]+>(.*?)<\/a>/s';      // 用户名
    $head_reg = '/<img src="(.*?)\"/s';        // 头像
    $time_reg = '/rel="(\d*)"/s';        // 发布时间
    $content_reg = '/<div class="msgCnt">(.*?)<\/div>/s';     //内容
    $pic_reg = '/<img class="crs" show="1" crs="(.*?)"/s';
    $isv_reg = '/<a title="(.*?)"/s';

    preg_match($mid_reg, $li[0], $mid_result); 
    if(empty($mid_result)) {
        continue;
    }

    preg_match($uname_reg, $li[0], $uname_result);
    preg_match($head_reg, $li[0], $head_result);
    preg_match($time_reg, $li[0], $time_result);
    preg_match($content_reg, $li[0], $content_result);
    preg_match($pic_reg, $li[0], $pic_result);
    preg_match($isv_reg, $li[0], $isv_result);

    $url_result = "http://t.qq.com/p/t/{$mid_result[1]}";       // 构造微博url

    $final_result[] = array(
        'mid' => $mid_result[1],
        'uname' => trim(strip_tags($uname_result[1])),
        'head' => $head_result[1],
        'url' => $url_result,
        'time' => $time_result[1],
        'author'=>trim(strip_tags($uname_result[1])),
        'source'=>1,
        'content' => trim(strip_tags($content_result[1])),     // 删除标签和前后空格
        'pic_url' => empty($pic_result[1])?'':$pic_result[1],
        'isv' => empty($isv_result[1])?'':$isv_result[1],
        'media' => '腾讯微博',
    );
}

$response_result = $final_result;
file_put_contents('weibo2-result', var_export($final_result, true));
