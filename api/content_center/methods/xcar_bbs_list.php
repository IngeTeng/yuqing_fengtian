<?php

/**
 * @filename xcar_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2016-12-16  14:38:38
 * @updatetime 2016-12-16  14:38:38
 * @version 1.0
 * @Description
 * 搜狗bbs搜索列表
 */

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/xcar_bbs_log.txt'); //将出错信息输出到一个文本文件 

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/bbs_list/spider_result/06d86b0a2d1ba1cf49fe4fc635d29ace";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/"bbsList":\[(.*)\],/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);
//print_r($main_result);

$li_reg = '/{(.*?)},/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//print_r($li_result);

foreach($li_result as $value) {

    if(strpos($value[1], 'enquire')){//提问帖的正则不一样，跳过提问帖
        continue;
    }
    $url_reg     = '/"post_url":"(.*?)"/s';
    $time_reg    = '/"publish_time":"(.*?)"/s';
    $title_reg   = '/"title":"(.*?)"/s';
    $content_reg = '/"abstract_bbs":"(.*?)"/s';
	$media_reg   = '/"fourm_name":"(.*?)"/s';
    $reply_reg   = '/"replies":"(.*?)"/s';
    $click_reg   = '/"views":"(.*?)"/s';
    $author_reg  = '/"author":"(.*?)"/s';

    preg_match($url_reg, $value[1], $url_result);
    
    preg_match($time_reg, $value[1], $time_result);
    preg_match($title_reg, $value[1], $title_result);
    preg_match($content_reg, $value[1], $content_result);
    preg_match($media_reg, $value[1], $media_result);
    preg_match($reply_reg, $value[1], $reply_result);
    preg_match($click_reg, $value[1], $click_result); 
    preg_match($author_reg, $value[1], $author_result);    
    //print_r($url_result);
    //print_r($time_result);
    // print_r($title_result);
    // print_r($content_result);
     //print_r($media_result);
    //print_r($reply_result);
    //print_r($click_result);
    //print_r($author_result);

    $title = trim(strip_tags($title_result[1]));
    $content = trim(strip_tags($content_result[1]));
    $author = trim(strip_tags($author_result[1]));

    // //获取详细时间
    // $html2 = file_get_contents($url_result[1]);
    // file_put_contents('xcar-html', $html2);
    // $time_reg    = '/发表于(.*?)<\/div>/s';
    // preg_match($time_reg, $html2, $time_result2);
    // file_put_contents('xcar-timel', $time_result2[1]);

    if(!empty($time_result[1])) {//获取详细时间失败
        $pubtime = strtotime($time_result[1].' 12:00:00');
           
    }
    // else{
    //     // 处理时间
    //     $time_str = trim(strip_tags($time_result2[1]));
    //     $pubtime = strtotime($time_str);
    // }

	//print_r($url);
	// if(strpos($url_result[1], '?') && !strpos($url_result[1], '?tid=')) {	// 如果存在get参数，并且不是默认的tid舍去
 //    	continue;
	// }

	
	
    $final_result[] = array(
        'url'     => $url_result[1],     
        'title'   => $title,        
        'time'    => $pubtime,     // 文章发布时间
        'content' => $content,
        'summary' => $content,
        'reply'   => $reply_result[1],
        'click'   => $click_result[1],
        'source'=>1,
        'author'  => $author,
        'forum'   => '爱卡汽车论坛搜索',
        'media'   => $media_result[1],
    );
}
//print_r($final_result);
$response_result = $final_result;
file_put_contents('xcar-bbs-list', var_export($final_result, true));