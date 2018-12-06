<?php

/**
 * @filename db.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-6-19  20:17:15
 * @updatetime 2016-6-19  20:17:15
 * @version 1.0
 * @Description
 * 数据库网关
 * 
 */


require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/log.php');
require(BASE_PATH. '/include/observer.php');

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
ini_set('error_log', dirname(__FILE__) . '/error_log.txt'); //将出错信息输出到一个文本文件

$log = Log::Init(LOG_HANDLER, LOG_LEVEL);

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $post_array = $_POST;
    $post_str = json_encode($post_array);
}
else {
    $log->WARN('post_str 为空');
    exit('hhhh');
}
file_put_contents('$post_array', var_export($post_array,true));

// 初始化返回
$response = array(
    'status' => 200,
    'timestamp' => time(),
);

// 创建观察者
$subject = new CInterfaceSubject();
$observer = new CInterfaceObserver();

// 将初始化的返回付给观察者
$observer->setResponse($response);
$subject->attach($observer);

// 获取sign并且unset
$sign = $post_array['sign'];
unset($post_array['sign']);

$signArr = $post_array;
unset($signArr["result"]);

if(strcmp($sign, Post2Sign::getSign($signArr, SECRET))) {
    // 403为sign错误

    $log->WARN("403 sign错误 $post_str");
    $subject->setStatus(403);

}
else {
    $collect_kind = $post_array['collect_kind'];      // 采集数据类型
    $collect_content_kind = $post_array['collect_content_kind'];      // 采集数据的内容
    $function_name = "$collect_kind". "_$collect_content_kind";
    
    if($post_array['sql'] == 'insert') {
        require(BASE_PATH. '/include/insert_methods.php');
    }
    elseif($post_array['sql'] == 'select') {
        require(BASE_PATH. '/include/select_methods.php');
    }
    
    if(function_exists($function_name)) {
        $log->INFO("200 找到解析函数 $function_name");
        $db_result = $function_name($post_array['result'], $log, $post_array['keyword']);
        if($db_result === false) {
            $subject->setStatus(500);
        }
    }
    else {
        $log->WARN("404 找不到该解析函数 $function_name,  keyword:".$post_array['keyword']);//删除$post_str
        $subject->setStatus(404);
    }
}
if($post_array['sql'] == 'insert') {
    $log->INFO("200 数据库保存成功 keyword：".$post_array['keyword']);//删除$post_str
}
else {
    $log->INFO("200 数据可以入库");
}
$json_result = json_encode($response);

echo $json_result;
