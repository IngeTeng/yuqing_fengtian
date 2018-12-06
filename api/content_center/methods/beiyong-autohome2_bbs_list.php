<?php

/**
 * @filename beiyong-autohome_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl
 * @createtime 
 * @updatetime 
 * @version 1.0
 * @Description
 * 汽车之家论坛主页搜索列表解析
 * 
 */

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/autohome2_bbs_log.txt'); //将出错信息输出到一个文本文件 
// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/bbs_list/spider_result/16d5ba8f09ddba73f75d72e65dad5a2f";
//$collect_download_url = "https://club.autohome.com.cn/bbs/forum-c-3126-1.html?orderby=dateline&qaType=-1";

$html = file_get_contents($collect_download_url);
$final_result = array();
//$html=iconv("GBK", "UTF-8//IGNORE", $html);
file_put_contents('auto2-html', $html);
//print_r($html);
$main_reg = '/<div id="subcontent"(.*)<\/div>/s';
//file_put_contents('autohome-bbs-html', $html);
// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);
// 懒惰匹配    
$li_reg = '/<dl class="list_dl"(.*?)<\/dl>/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);
foreach($li_result as $value) {
    //print_r($value);
    $url_reg = '/target="_blank" href="([^"]+)"/s';
    $title_reg = '/<dt>(.*?)<\/dt>/s';        // 标题
    //$content_reg = '/<dd>(.*?)<\/dd>/s';      // 内容
    $time_reg = '/<span class="ttime">(.*?)<\/span>/s';//时间
    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($time_reg, $value[0], $time_result);

    $title = trim(strip_tags($title_result[1]));
    $content = $title;
    $url = 'club.autohome.com.cn'.$url_result[1];
    //print_r($title_result);
    //获取文章详细时间
    //$html2 = get_html($url);
    //preg_match($time_reg2, $html2, $time_result2);

    $time = strtotime($time_result[1]);
    
    
    $final_result[] = array(            
        'url' => $url,     
        'title' => $title,        // 删除标签
        'time' => $time,     // 文章发布时间
        'content' => $content,
        'summary' => $content,
        'reply' => 0,
        'click' => 0,
        'author' => '',
        'forum' => '',
        'media' => '汽车之家',
    );
    // 删除上一次的记录
    unset($author_result);
    unset($forum_result);
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('autohome2-bbs-res', var_export($final_result, true));
