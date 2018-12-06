<?php

/**
 * @filename ifeng_result.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-19  14:49:14
 * @updatetime 2016-8-19  14:49:14
 * @version 1.0
 * @Description
 * 处理触发反爬虫的结果
 * 
 */

require_once('../config.php');
require_once(BASE_PATH. '/bbs_article/config.php');

use SPIDER_CENTER\BBS_ARTICLE\Configure;

$save_result = array(
    'collect_from_site' => 'ifeng',
    'collect_content_kind' => 'article',
    'collect_kind' => 'bbs',
    'keyword' => '',
);

// 处理get请求
if(isset($_GET['filepath']) and isset($_GET['str'])) {
    $str = $_GET['str'];
    $filepath = $_GET['filepath'];
    // 获取文件�?
    if(is_file($filepath)) {
    $fileinfo = explode('/', $filepath);
    $filename = $fileinfo[count($fileinfo)-1];
	
    $temp_result = file_get_contents($filepath);
    $temp_array = json_decode($temp_result, true);
    $temp_array['url'] = 'http://bbs.auto.ifeng.com'. $str;
    // 保存解析结果
    if (is_dir(RESULT_PATH)) {
        
        $result_path = RESULT_PATH. "/$filename.temp";
        $save_result['result'] = $temp_array;
        $file_result = file_put_contents($result_path, json_encode($save_result). "\r\n", FILE_APPEND);

        // 如果保存成功, 修改文件�?
        if($file_result === false) {
            exit('文件保存失败');
        }
        else {
            rename($result_path, RESULT_PATH. "/$filename");
        }
    }
    
    // 处理爬取结果
    if(SPIDER_FILE_AFTER_TREATING == 2) {
        // 删除
        unlink($filepath);
    }
    else if(SPIDER_FILE_AFTER_TREATING == 3) {
        // 转移
        rename($filepath, SPIDER_PATH_AFTER_TREATING. "/$filename");
    }
    } 
}

// 遍历文件�?
begin:
$ifeng_result_path = Configure::get_ifeng_result_path(BASE_PATH);
while(true) {
    $files = glob($ifeng_result_path. '/*.json');
    if(empty($files)) {
        exit('反爬虫结果文件处理完');
    }
    $filepath = $files[0];
    $result_str = file_get_contents($filepath);
    $result_array = json_decode($result_str, true);

    $url = $result_array['url'];
    $content = file_get_contents($url);

    if(strpos($content, '指定的主题不存在或已被删除或正在被审') !== false or strpos($content, '凤凰汽车论坛') !== false) {
        unlink($filepath);
    }
    else {
        break;
    }
}

if(substr_count($content, 'location') == 1) {
    $reg = "/(.*?)\s*=\s*location/";
    preg_match($reg, $content, $result);
    $location = substr($result[1], -6);     // 随机生成变量都是 _xxxxx
}
else {
    $location = 'location';
}
$reg1 = "/$location([^;&^\s]*?)[|(|=]/";
preg_match($reg1, $content, $result);

// 插入跳转字符�?
$p = strpos($content, $result[0]);
$len = strlen($result[0]);
$pre_str = substr($content, 0, $p+$len);
$aft_str = substr($content, $p+$len);
$str = $pre_str. "'ifeng_result.php?filepath=$filepath&str='+". $aft_str;

echo $str;      // 执行js
