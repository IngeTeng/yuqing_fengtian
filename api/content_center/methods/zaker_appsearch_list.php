<?php

/**
 * @filename zaker_appsearch_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-10  20:39:01
 * @updatetime 2016-11-10  20:39:01
 * @version 1.0
 * @Description
 * zaker搜索
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/appsearch_list/spider_result/d12d90ecfacefcd904906c54c9f3d9b4";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div id="content">(.*?)<div id="aside"/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

// 懒惰匹配    
$li_reg = '/<div class="article flex-1">(.*?)<div class="figure flex-block">/s';

// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {

    // 构建正则表达式
    $url_reg = '/<a href="\/\/(.*?)"/s';
    $title_reg = '/title="(.*?)"/s';
    $from_reg = '/<span>(.*?)<\/span>/s';
    $time_reg = '/<\/span><span>(.*?)<\/span>/s';

    preg_match($url_reg, $value[0], $url_result);
    preg_match($title_reg, $value[0], $title_result);
    preg_match($from_reg, $value[0], $from_result);
    preg_match($time_reg, $value[0], $time_result);
    
    // 处理时间
    $time_str = '';
    if(!empty($time_result[1])) {
        $time_str = $time_result[1];
        
        $p = strpos($time_str, "小时");
        $sec = 3600;
        
        if ($p !== false) {
            $dd = substr($time_str, 0, $p);
            $pubtime = time() - $dd * $sec;
        } else {
            $p = strpos($time_str, '昨天');
            $sec = 84600;
            if($p === false) {
                $p = strpos($time_str, '前天');
                $sec = 84600*2;
            }
            if ($p !== false) {
                $pubtime = time() - $sec;
            } else {
                $time_str = '2018-'. $time_result[1]. ' 08:00:00';
                $pubtime = strtotime($time_str);
            }
        }   
    }
    
    $final_result[] = array(
        'url' => 'http://'. $url_result[1],
        'title' => trim(strip_tags($title_result[1])),
        'from' => 'Zaker',
        /**************/
        'author'=>"",
        'source'=>1,
        /******************/
        'time' => $pubtime,
        'channel' => $from_result[1],
    );
}

$response_result = $final_result;
file_put_contents('zaker-apps-list', var_export($response_result));