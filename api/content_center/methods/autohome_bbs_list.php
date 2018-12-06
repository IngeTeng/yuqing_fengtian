<?php

/**
 * @filename autohome_bbs_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-15  21:48:44
 * @updatetime 2016-8-15  21:48:44
 * @version 1.0
 * @Description
 * 汽车之家论坛搜索列表解析
 * 
 */


ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/autohome_bbs_log.txt'); //将出错信息输出到一个文本文件 
// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/bbs_list/spider_result/16d5ba8f09ddba73f75d72e65dad5a2f";
//$collect_download_url = "http://sou.autohome.com.cn/luntan?q=%BF%AD%C3%C0%C8%F0&pvareaid=100834&entry=44&clubClassBefore=0&IsSelect=0&clubOrder=New&clubClass=0&clubSearchType=&clubSearchTime=&pq=%25s&pt=636312223671779902";
$html = file_get_contents($collect_download_url);
$final_result = array();
//$html=iconv("GBK", "UTF-8//IGNORE", $html);
$main_reg = '/<div class="result"(.*)<\/div>/s';
//file_put_contents('autohome-bbs-html', $html);
// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/<dl class="list-dl"(.*?)<\/dl>/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {

    $url_reg = '/<a href="([^"]+)"/s';
    $title_reg = '/<dt>(.*?)<\/dt>/s';        // 标题
    $content_reg = '/<dd>(.*?)<\/dd>/s';      // 内容
    $info_reg = '/<dd class="infodd">(.*?)<\/dd>/s';

    preg_match($info_reg, $value[0], $bbs_info);
    if(empty($bbs_info)) {
        continue;
    }
//  update 发表于 ：
    $str = iconv('utf-8', 'gb2312', '发表于');

    $time_reg = "/$str(.*?)<\/span>/s";

    $time_reg2 = '/xname="date">(.*?)<\/span>/s';//获取详细内容页面的时间专用

    $time_reg3 = '/<span class="zhidaopad">(.*?)<\/span>/s';//问答专用

    $author='/class="c01439a" title=(.*?) xname="uname"/s';

    preg_match($content_reg, $value[0], $content_result);
    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($time_reg, $bbs_info[0], $time_result1);
    /***************************************/
    preg_match($author, $value[0], $author_name);
    /***************************************/
    //2018-01-18 解决部分数据（论坛问答数据）在论坛搜索模块抓不到
    if(empty($time_result1[1])){
        preg_match($time_reg3, $bbs_info[0], $time_result1);//问答只有时间匹配不一样
    }
    if(empty($content_result[1])){
        $content_result[1] = $title_result[1];//论坛问答抓取中有时候会没有内容
    }

    $content = iconv("gbk", "utf-8", trim(strip_tags($content_result[1])));
    $title = iconv("gbk", "utf-8", trim(strip_tags($title_result[1])));
	
	$url = $url_result[1];
	$p = strpos($url_result[1], '?');
	if($p > 0) {
		$url = substr($url_result[1], 0, $p);
	}
    //过滤活动报名信息页面
    if(strpos($url_result[1], 'PartyRegisterList')) {
        continue;
    }
    //获取文章详细时间
    $html2 = get_html($url);
    preg_match($time_reg2, $html2, $time_result2);
    // if(empty($time_result2)){
    //     $html2 = get_html($url);
    //     preg_match($time_reg2, $html2, $time_result2);
    // }
    if(empty($time_result2)){
        $ltime = strtotime($time_result1[1].' 15:00:00');
        if(time() > $ltime){//如果抓取的时间是
            $time = strtotime($time_result1[1].' 14:00:00');
        }else{
            $time = strtotime($time_result1[1].' 08:00:00');//部分结果出现乱码，获取不到时间
        }
        
    }else{
        $time = strtotime($time_result2[1]);
    }
	if(!$author_name){
        $author_name="";
    }

    $final_result[] = array(            
        'url' => $url,     
        'title' => $title,        // 删除标签
        'time' => $time,     // 文章发布时间
        'content' => $content,
        'summary' => $content,
        /**************/
        'author'=>$author_name,
        'source'=>1,
        /******************/
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
 //file_put_contents('autohome-bbs-res', var_export($final_result, true));
file_put_contents('author.txt',$author_name,FILE_APPEND);
file_put_contents('autohome-bbs-luist', var_export($final_result, true));
