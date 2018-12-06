<?php

/**
 * @filename auto_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-14  17:35:30
 * @updatetime 2016-8-14  17:35:30
 * @version 1.0
 * @Description
 * 汽车之家api列表解析
 * 
 */
ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/autohome_app_log.txt'); //将出错信息输出到一个文本文件 

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "E:/wamp64/www/yq/wii_spider/app_list/spider_result/09715b53517bf2b73af8ab796f666acd";

$json_str = file_get_contents($collect_download_url);
$final_result = array();
//print_r($json_str);
if(!empty($json_str)) {
    $json_array = json_decode($json_str, true);
    $result_array = $json_array['result'];
   // print_r($result_array);
    if(!empty($result_array['headlineinfo'])) {
        $head_info = $result_array['headlineinfo']['data'];
        //转换时间字符串
        $timestr = '';
        for ($i=0; $i < 14; $i++) { 
            if($i == 4 or $i == 6){
                $timestr = $timestr. '-'.$head_info['updatetime'][$i];
            }else if($i == 8){
                $timestr = $timestr. ' '.$head_info['updatetime'][$i];
            }else if($i == 10 or $i == 12){
                $timestr = $timestr. ':'.$head_info['updatetime'][$i];
            }else{
                $timestr .= $head_info['updatetime'][$i];
            }
        }
        
        $final_result[] = array(
            'id' => $head_info['id'],
            'title' => $head_info['title'],
            'url' => $head_info['id'],
            'time' => strtotime($timestr),
            'author'=>$data['thirdsource'],
            'source'=>1,
            'media' => '汽车之家',
            'channel' => $head_info['medianame'],
            'mediatype' => $head_info['mediatype'],
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }
    
    if(!empty($result_array['focusimg'])) {
        $focus_infos = $result_array['focusimg']['data'];
        foreach($focus_infos as $data) {
            //转换时间字符串
            $timestr = '';
            for ($i=0; $i < 14; $i++) { 
                if($i == 4 or $i == 6){
                    $timestr = $timestr. '-'.$data['updatetime'][$i];
                }else if($i == 8){
                    $timestr = $timestr. ' '.$data['updatetime'][$i];
                }else if($i == 10 or $i == 12){
                    $timestr = $timestr. ':'.$data['updatetime'][$i];
                }else{
                    $timestr .= $data['updatetime'][$i];
                }
            }
        
            $final_result[] = array(
                'id' => $data['id'],
                'title' => $data['title'],
                'url' => $data['id'],
                'time' => strtotime($timestr),
                'author'=>$data['thirdsource'],
                'source'=>1,
                'media' => '汽车之家',
                'channel' => $data['medianame'],
                'mediatype' => $data['mediatype'],
                'collect_from_site' => $post_obj['collect_from_site'],
            );
        }
    }
    
    if(!empty($result_array['newslist']) ) {

        $news_list = $result_array['newslist'];
       
        foreach($news_list as $data) {
            //print_r($data);
            $data = $data['data'];
            //转换时间字符串
            $timestr = '';
            for ($i=0; $i < 14; $i++) { 
                if($i == 4 or $i == 6){
                    $timestr = $timestr. '-'.$data['updatetime'][$i];
                }else if($i == 8){
                    $timestr = $timestr. ' '.$data['updatetime'][$i];
                }else if($i == 10 or $i == 12){
                    $timestr = $timestr. ':'.$data['updatetime'][$i];
                }else{
                    $timestr .= $data['updatetime'][$i];
                }
            }
            $final_result[] = array(
                'id' => $data['id'],
                'title' => $data['title'],
                'url' => $data['id'],
                'time' => strtotime($timestr),
                'author'=>$data['thirdsource'],
                'source'=>1,
                'media' => '汽车之家',
                'channel' => $data['medianame'],
                'mediatype' => $data['mediatype'],
                'collect_from_site' => $post_obj['collect_from_site'],
            );
        }
    }
    if( !empty($result_array['list'])){
        $news_list = $result_array['list'];    
        foreach($news_list as $data) {
            //print_r($data);

            $final_result[] = array(
                'id' => $data['imageid'],
                'title' => $data['title'],
                'url' => $data['imageid'],
                'time' => strtotime('2018-'.$data['publistime'].' 08:00:00'),
                'author'=>$data['thirdsource'],
                'source'=>1,
                'media' => '汽车之家',
                'channel' => '图说',
                'mediatype' => $data['typeid'],
                'collect_from_site' => $post_obj['collect_from_site'],
            );
        }
    }
}

$response_result = $final_result;
file_put_contents('auto-app.txt', var_dump($final_result),FILE_APPEND);
//print_r($final_result);

