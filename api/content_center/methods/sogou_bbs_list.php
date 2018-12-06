<?php

/**
 * @filename sogou_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-8  20:38:38
 * @updatetime 2016-8-8  20:38:38
 * @version 1.0
 * @Description
 * 搜狗bbs搜索列表
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing2/wii_spider/bbs_list/spider_result/1f4ff9dcfaa607f847054e5cf0a19c13";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="results" >(.*)<\/div>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

$li_reg = '/class="(vrwrap|rb)">(.*?)<\/div>\s*<\/div>/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {

    $url_reg = '/href="(.*?)"/s';
    $time_reg = '/&nbsp;-&nbsp;(.*?)&nbsp;-&nbsp;/s';
    $title_reg = '/cacheStrategy="qcr:-1">(.*?)<\/a>/s';
    $content_reg = ($value[1]=='rb')?'/id="cacheresult_summary_[\d]+">(.*?)<\/div>/s':'/<p class="str_info">(.*?)<\/p>/s';
	$media_reg = '/<cite id="cacheresult_info_[\d]+">(.*?)-/';

    preg_match($url_reg, $value[2], $url_result);
    
    if(strpos($url_result[1], 'bbs.auto.sina.com.cn') !== false
    	and strpos($url_result[1], 'forum') !== false) {
    	continue;
    }
    
    preg_match($time_reg, $value[2], $time_result);
    preg_match($title_reg, $value[2], $title_result);
    preg_match($content_reg, $value[2], $content_result);
    preg_match($media_reg, $value[2], $media_result);
    
    if(strpos($media_result[1], 'https') !== false
            or strpos($media_result[1], 'www') !== false) {
        continue;
    }

    $title = trim(strip_tags($title_result[1]));
    $content = trim(strip_tags($content_result[1]));
    // 处理时间
    $time_str = trim(strip_tags($time_result[1]));
    if(!empty($time_str)) {
        $p = strpos($time_str, "分钟");
        $sec = 60;
        if(!$p) {
            $p = strpos($time_str, "小时");
            $sec = 3600;
        }
        if($p > 0) {
            $dd = substr($time_str, 0, $p);
            $pubtime = time() - $dd * $sec;
        }
        else {
            $pubtime = strtotime($time_str);
        }   
    }
	//print_r($url);
	if(strpos($url_result[1], '?') && !strpos($url_result[1], '?tid=')) {	// 如果存在get参数，并且不是默认的tid舍去
    	continue;
	}

	if(strpos($url_result[1], 'http:/', 6)) {		// 去除搜狗搜索的垃圾信息
    	continue;
	}
    if(strpos($url_result[1], 'info.xcar.com.cn/?tid=')) {   // 去除重复的无用的爱卡汽车网
        continue;
    }
    if(strpos($url_result[1], 'bbs.cheshi.com/forum')) {   // 去除重复的无用的网上车市网
        continue;
    }
	if(strpos($url_result[1], 'www', 10)) {
		continue;
	}
	if(empty($pubtime)){
        //echo "搜狗论坛搜索文章时间为空";
        continue;
    }
    if(strstr($url_result[1], 'bbs') == false){
        //echo "不是bbs";
        continue;
    }
	
    $final_result[] = array(
        'url' => $url_result[1],     
        'title' => $title,        
        'time' => $pubtime,     // 文章发布时间
        'content' => $content,
        'summary' => $content,
        'reply' => 0,
        'click' => 0,
        'author' => '',
        'forum' => '搜狗论坛搜索',
        'media' => $media_result[1],
    );
}
//print_r($final_result);
$response_result = $final_result;