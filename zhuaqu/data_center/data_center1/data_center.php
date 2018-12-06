<?php

/**
 * @filename data_center.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-6-15  15:52:53
 * @updatetime 2016-6-15  15:52:53
 * @version 1.0
 * @Description
 * 
 */

// 引入配置文件 工具类 函数库
require('./config.php');
require(BASE_PATH. 'include/post2sign.php');
require(BASE_PATH. 'include/log.php');
require(BASE_PATH. 'include/function.php');
require(BASE_PATH. 'include/methods.php');

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/error_log.txt'); //将出错信息输出到一个文本文件 

while(true) {
	$log_handler = LOG_HANDLER. date('Y_m_d', time());
	$log = Log::Init($log_handler, LOG_LEVEL);

    //$path2 = '/home/wiipu/local/apache/htdocs/data_center/fail/'; //重新扫描一遍fail目录
    // 在目标目录下查找所有未处理的结果
    $files = glob(TARGET_PATH. '*.json');
    //$files = glob($path2. '*.json');
 //   $log->INFO("begin time: ". time());
    
    if(count($files) < 100) {
    	sleep(10);
    }
    //rsort($files);   // 1.逆序
	//sort($files);    // 2.正序
    shuffle($files);	// 3.乱序
    foreach($files as $file) {
        $temp_array = array();
        if(!is_file($file)) {
            continue;
        }
        
        $result_str = file_get_contents($file);
        //echo $result_str, PHP_EOL;
        // 解析为数组
        $result_array = json_decode($result_str, true);

        $collect_kind = $result_array['collect_kind'];      // 采集数据类型
        // if($collect_kind == 'app'){
        //     continue;
        // }
        $collect_content_kind = $result_array['collect_content_kind'];      // 采集数据的内容
        $function_name = $collect_kind."_".$collect_content_kind;
        //echo $function_name, PHP_EOL;
        
        if(function_exists($function_name)) {
            $log->INFO("找到对应解析函数, $function_name, collect_from_site: {$result_array['collect_from_site']}, keyword:".$result_array['keyword']);
            $request = $function_name($result_array, $REPLACE_STRS);
//            print_r($request);
            if(empty($request['result'])) {
                $log->WARN("没有需要存储的数据, keysord:".$result_array['keyword']);//删除$result_str
                // echo '<br>没有需要存储的数据<br>';
                if(is_file($file)) {
                	unlink($file);
                }
                continue;
            }
            $request['sql'] = 'insert';
            $request['keyword'] = $result_array['keyword'];
            $result_str = json_encode($request);
            
            // 进行加密
            unset($request['sign']);

            $signArr = $request;
            unset($signArr["result"]);
            
            $request['sign'] = Post2Sign::getSign($signArr, SECRET);

            // 发送请求
            $response = send_post(DATABASE_CENTER, $request);

            // echo "$response", PHP_EOL;
            if(!empty($response)) {
                $response_array = json_decode($response, true);
                if($response_array['status'] == REQUEST_OK) {
                    $log->INFO("数据库保存成功, keyword:".$result_array['keyword']);//删除$result_str
                    // 如果配置为将处理后的文件转移
                    if(FILE_AFTER_TREATING == 1) {
                        if(!is_dir(PAHT_AFTER_TREATING)) {
                            mkdir(PAHT_AFTER_TREATING); 
                        }
                        $file_name = str_replace(TARGET_PATH, '', $file);
                        // 使用rename进行文件转移,速度快
                        rename($file, PAHT_AFTER_TREATING. $file_name);
                    }
                    elseif(FILE_AFTER_TREATING == 2) {
                        if(is_file($file)) {
                            unlink($file);
                        }
                    }
                }
                else {
                    $log->WARN("请求失败, 返回为{$response_array['status']}");
                    echo "$response", PHP_EOL;
                    // 请求失败进行转移
                    if (!is_dir(BASE_PATH. 'fail/')) {
                        mkdir(BASE_PATH. 'fail/'); 
                    }
                    $file_name = str_replace(TARGET_PATH, '', $file);
                    // 使用rename进行文件转移,速度快
                    rename($file, BASE_PATH. 'fail/'. $file_name);
                   // unlink($file);
                }
            }
            else {
                $log->WARN("请求失败, 返回为空");
            }
        }
        else {
            $log->WARN("找不到对应的解析函数, $function_name");
            continue;
        }
        //break;
    }
    //break;
   // $log->INFO("end time: ". time());
    unset($log);
    unset($log_handler);
//    sleep(SLEEP_TIME);
}
