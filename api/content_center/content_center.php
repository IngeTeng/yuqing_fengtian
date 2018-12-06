<?php

/**
 * @filename content_center.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @datetime 2016-6-2  14:29:17
 * @version 1.0
 * @Description
 * 
 */

require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/observer.php');
require(BASE_PATH. '/include/log.php');


// ini_set('display_errors',1);            //错误信息  
// ini_set('display_startup_errors',1);    //php启动错误信息  
// error_reporting(-1);                    //打印出所有的 错误信息  
// ini_set('error_log', dirname(__FILE__) . '/content_log.txt'); //将出错信息输出到一个文本文件 

//echo LOG_HANDLER;

// 初始化log类
$log = Log::Init(LOG_HANDLER, LOG_LEVEL);

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $post_obj = $_POST;
    $post_str = json_encode($post_obj);
}
else {
    $log->WARN('post_str 为空');
    exit('hhhh');
}

// 初始化返回
$response = array(
    'status' => 200,
    'timestamp' => time(),
    'result' => '',
);

// 创建观察者
$subject = new CInterfaceSubject();
$observer = new CInterfaceObserver();
// 将初始化的返回付给观察者
$observer->setResponse($response);
$subject->attach($observer);

// 获取sign并且unset
$sign = $post_obj['sign'];
unset($post_obj['sign']);

if(strcmp($sign, Post2Sign::getSign($post_obj, SECRET)) !== 0) {
    // 403为sign错误
    $log->WARN(" 403 sign错误 $post_str");
    $subject->setStatus(403);
}

$collect_kind = $post_obj['collect_kind'];
$collect_from_site = $post_obj['collect_from_site'];
$collect_content_kind = $post_obj['collect_content_kind'];

$method_name = BASE_PATH. "/methods/$collect_from_site". "_$collect_kind" ."_$collect_content_kind.php";

if(file_exists($method_name)) {
    $log->INFO(" 200 成功 $post_str");
    include($method_name);
}
else {
    $log->WARN("404 没有找到对应的解析函数 $post_str");
    $subject->setStatus(404);
}

if(empty($response_result)) {
    $subject->setStatus(501);
}

$response['result'] = $response_result;
// 本地存储的数据
$local_result = json_encode(array_merge($post_obj, $response));
// 返回数据格式化
$json_result = json_encode($response);


// 保存本地数据
if(SAVE_RESULT == 1) {
    if(!is_dir(RESULT_PATH)) {
        mkdir(RESULT_PATH);
    }
    $result_name = hash("md5", $local_result);
    $result_path = RESULT_PATH. "/$result_name.temp.json";
    $file_result = file_put_contents($result_path, $local_result. "\r\n", FILE_APPEND);
    // 如果保存成功,修改文件名    
    if($file_result === false or filesize($result_path) < 10) {
        $log->WARN("本地结果保存失败 $post_str");
        // 删除文件
        unlink($result_path);
    }
    else {
        rename($result_path, RESULT_PATH. "/$result_name.json");
        $log->INFO("本地结果保存成功 文件名为: $result_name.json");
    }
}

echo $json_result;
