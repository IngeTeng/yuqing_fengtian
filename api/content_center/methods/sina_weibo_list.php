<?php

/**
 * @filename search_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-7-18  13:37:28
 * @updatetime 2016-7-18  13:37:28
 * @version 1.0
 * @Description
 * 解析微博搜索结果
 * 
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing2/wii_spider/weibo_list/spider_result/a90cd5b11306779497f7059bab175849";
//$collect_download_url = "http://s.weibo.com/weibo/%E9%9B%85%E5%8A%9B%E5%A3%AB&nodup=1&xsort=time";

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/weibo_log.txt'); //将出错信息输出到一个文本文件 

$html = file_get_contents($collect_download_url);

//file_put_contents('weibo-html', $html);

$final_result = array();

$li_reg = '/<div class=\\\"WB_cardwrap S_bg2 clearfix(.*?)<div class=\\\"feed_action/s';
preg_match_all($li_reg, $html, $li_result, PREG_SET_ORDER);

if(empty($li_result)) {
    $li_reg = '/<dl class="feed_list"(.*?)<dd class="clear">/s';
    preg_match_all($li_reg, $html, $li_result, PREG_SET_ORDER);
    
    foreach($li_result as $li) {
        $mid_reg = '/mid="([^"]+)"/s';     // mid
        $uname_reg = '/title="([^"]+)"/s';      // 用户民
        $head_reg = '/<img src="([^"]+)"/s';        // 头像
        $url_reg = '/<\/span>\s*<a href="([^"]+)"/s';      // 微博地址
        $uid_reg = '/weibo.com\/(.*?)\//';     // 微博id
        $time_reg = '/date="([^"]+)"/';        // 发布时间
        $content_reg = '/<p node-type="feed_list_content">(.*?)<\/p>/s';     //内容
        $isv_reg = '/verify" title= "([^"]+)"/s';

        preg_match($mid_reg, $li[0], $mid_result); 
        if(empty($mid_result)) {
            continue;
        }
        preg_match($uname_reg, $li[0], $uname_result);
        preg_match($head_reg, $li[0], $head_result);
        preg_match($url_reg, $li[0], $url_result);
        if(empty($url_result)){
            continue;
        }
        preg_match($uid_reg, $url_result[0], $uid_result);
        preg_match($time_reg, $li[0], $time_result);
        preg_match($content_reg, $li[0], $content);
        preg_match($isv_reg, $li[0], $isv_result);

        $content_result = $content[1];
        $pos = strpos($content_result, '>');
        if($pos > 0) {
            $content_result = (substr($content[1], $pos+1));
        }

        $url = stripslashes($url_result[1]);
        $p = strpos($url, '?');
            if($p > 0) {
                    $url = substr($url, 0, $p);
            }
        if(empty($time_result[1])){
            //echo "新浪微博时间为空";
            continue;
        }
        $final_result[] = array(
            'mid' => $mid_result[1],
            'uname' => trim(strip_tags($uname_result[1])),
            'head' => stripslashes($head_result[1]),
            'url' => $url,
            'uid' => $uid_result[1],
            'author'=>trim(strip_tags($uname_result[1])),
            'source'=>1,
            'time' => $time_result[1]/1000, 
            'content' => trim(strip_tags($content_result)),     // 删除标签和前后空格
            'isv' => empty($isv_result[1])?'':$isv_result[1],
            'media' => '新浪',
        );
    }
}
else {
    foreach($li_result as $li) {
        $json = '{"a":"' . $li[0] .'"}';
        $j = json_decode($json);
        $li = $j->a;

        $mid_reg = '/<div mid="(.*?)"/s';     // mid
        $uname_reg = '/title="(.*?)"/s';      // 用户民
        $head_reg = '/<img src="(.*?)"/s';        // 头像
        //$url_reg = '/<div class="feed_from W_textb">\s*<a href="(.*?)"/s';      // 微博地址
        $url_reg = '/<div class="feed_from W_textb">\\n  <!-- 聚合微博title -->\\n <a href="(.*?)"/s';
        $uid_reg = '/weibo.com\/(.*?)\//';     // 微博id
        $time_reg = '/date="(.*?)"/';        // 发布时间
        $content_reg = '/<p class="comment_txt" node-type="feed_list_content"(.*?)<\/p>/s';     //内容
        $isv_reg = '/verify" title= "([^"]+)"/s';

        preg_match($mid_reg, $li, $mid_result); 
        if(empty($mid_result)) {
            continue;
        }
        preg_match($uname_reg, $li, $uname_result);
        preg_match($head_reg, $li, $head_result);
        preg_match($url_reg, $li, $url_result);
        if(empty($url_result)){
            continue;
        }
        preg_match($uid_reg, $url_result[0], $uid_result);
        preg_match($time_reg, $li, $time_result);
        preg_match($content_reg, $li, $content);
        preg_match($isv_reg, $li, $isv_result);

        $content_result = $content[1];
        $pos = strpos($content_result, '>');
        if($pos > 0) {
            $content_result = (substr($content[1], $pos+1));
        }

        $url = stripslashes($url_result[1]);
        $p = strpos($url, '?');
            if($p > 0) {
                    $url = substr($url, 0, $p);
            }

        if(empty($time_result[1])){
            //echo "新浪微博时间为空";
            continue;
        }

        $final_result[] = array(
            'mid' => $mid_result[1],
            'uname' => trim(strip_tags($uname_result[1])),
            'head' => stripslashes($head_result[1]),
            'url' => $url,
            'author'=>trim(strip_tags($uname_result[1])),
            'source'=>1,
            'uid' => $uid_result[1],
            'time' => $time_result[1]/1000, 
            'content' => trim(strip_tags($content_result)),     // 删除标签和前后空格
            'isv' => empty($isv_result[1])?'':$isv_result[1],
            'media' => '新浪微博',
        );
    }
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('weibo-result', var_export($final_result, true));