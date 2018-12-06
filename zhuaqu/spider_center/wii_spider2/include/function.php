<?php

/**
 * @filename function.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @datetime 2016-6-6  22:39:57
 * @version 1.0
 * @Description
 * 
 * 常用方法
 * 
 */


// 发送post请求
function send_post($url, $post_data) {  
    $postdata = http_build_query($post_data);
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // 设置不输出到屏幕 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,20); 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;  
} 

// 获取的html,带模拟登陆
function get_html($url, $cookie='', $proxy='', $proxy_port='', $referer='', $gzip=false) {
    $ch = curl_init();
    // 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//允许页面跳转，获取重定向
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);      // 60秒超时
    if($gzip) curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // 编码格式

    if($cookie != '') {
    	$coo = "Cookie:$cookie";
    	$headers[] = $coo;
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($referer != '') {
        curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
    if($proxy != '' and $proxy_port != '') {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    
    // 获取内容
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// 保存文件
function save_file($file_path, $file_name, $file_content) {
    // 处理导入字符串
    $path_len = strlen($file_path);
    if($file_path[$path_len-1] !== '/') {
        $file_path .= '/';
    }
    if($file_name[0] === '/') {
        $file_name = substr($file_name, 1);
    }
    
    // 先保存临时文件
    $temp_file = $file_path. "$file_name.temp";
    $file_result = file_put_contents($temp_file, $file_content, FILE_APPEND);

    // 如果保存成功,修改文件名    
    if($file_result === false) {
        return false;
    }
    else {
        rename($temp_file, $file_path. $file_name);
        return true;
    }
}

// 获取keywords
function get_keywords($spider_name, $collect_name) {
    // 任务中心请求初始化
    $task_request = array(
        'collect_name' => $collect_name,
        'spider_name' => '',
        'timestamp' => '',
        'request' => 1,     // 请求关键字
    );

    // 向任务中心请求关键字
    $task_request['spider_name'] = $spider_name;
    $task_request['timestamp'] = time();
    $task_request['sign'] = Post2Sign::getSign($task_request, SECRET);
    $task_response = send_post(TASK_CENTER, $task_request);
    return $task_response;
}

// 处理单个页面
function handle_one_page($spider_result, $spider_result_path, $content_request, $log) {
    require_once('mail.php');//报警器
    if(!is_dir($spider_result_path)) {
        mkdir($spider_result_path);
    }
    $spider_url = $content_request['collect_url']; 
    $key_word = $content_request['keyword'];
    $spider_result_name = hash("md5", json_encode($content_request));       // 生成唯一文件名
    $spider_file_result = save_file($spider_result_path, $spider_result_name, $spider_result);
    // 如果保存成功,修改文件名    
    if($spider_file_result === false) {
        $log->WARN("爬取结果保存失败 URL=$spider_url");

        $warnFile    = $content_request['collect_from_site'].'_'.$content_request['spider_kind'].iconv('UTF-8', 'GBK', '爬取结果保存失败');
        $warnContent = '爬取结果保存失败 '.$content_request['collect_from_site'].'_'.$content_request['spider_kind']."目录：".$spider_result_path;
       new Mail($warnFile, $warnContent);//发送报警

        return;
    }
    else {
        $filename = $spider_result_path. "/$spider_result_name";
        $log->INFO("爬取结果保存成功, keyword=$key_word, URL=$spider_url , FILENAME=". $filename );
        
  //       //删除内容中没有关键字的无效结果
  //       $content = file_get_contents($filename);
  //       if(!empty($key_word) && (strpos($content, $key_word) === false)){
  //           unlink($filename);
  //           $log->INFO("爬取结果没有关键字,已删除 keyword=$key_word, URL=$spider_url " );
		// return;
  //       }

    }

    $content_request['collect_download_url'] = 'http://'. COLLECT_HOST. ':'. COLLECT_PORT. '/wii_spider/'. $content_request['spider_kind']. "/spider_result/$spider_result_name";     // 拼接下载地址

    // 删除上一次的请求的sign
    unset($content_request['sign']);
    // 进行加密
    $content_request['sign'] = Post2Sign::getSign($content_request, SECRET);

    // 发送post请求
    $response = send_post(CONTENT_CENTER, $content_request);
    $response_array = json_decode($response, true);
    if($response_array['status'] != REQUEST_OK) {
        $log->WARN("解析请求失败 {$response_array['status']} 爬取结果为: $spider_result_name");
        $warnFile    =  $content_request['collect_from_site'].'_'.$content_request['spider_kind'].iconv('UTF-8', 'GBK', "解析请求失败{$response_array['status']}");
        $warnContent =  "解析请求失败 {$response_array['status']} ".$content_request['collect_from_site'].'_'.$content_request['spider_kind'];
        new Mail($warnFile, $warnContent);//发送报警

        return -2;
    }
    $local_result = json_encode(array_merge($response_array, $content_request));
    
    if(!is_dir(RESULT_PATH)) {
        mkdir(RESULT_PATH);
    }
    
    // 保存解析结果
    $result_name = hash("md5", $local_result). '.json';
    $file_result = save_file(RESULT_PATH, $result_name, $local_result);

    if($file_result === false) {
        $log->WARN("解析结果保存失败, DOWNLOAD_URL={$content_request['collect_download_url']}");

        $warnFile    = $content_request['collect_from_site'].'_'.$content_request['spider_kind'].iconv('UTF-8', 'GBK', "解析结果保存失败");
        $warnContent = "解析结果保存失败 ".$content_request['collect_from_site'].'_'.$content_request['spider_kind'];
        new Mail($warnFile, $warnContent);//发送报警
        
        return;    
    }
    else {
        $log->INFO("解析结果保存成功, FILENAME=". RESULT_PATH. "/$result_name");
    }

    // 处理爬取结果
    if(SPIDER_FILE_AFTER_TREATING == 2) {
        // 删除
        unlink($spider_result_path. "/$spider_result_name");
    }
    else if(SPIDER_FILE_AFTER_TREATING == 3) {
        if(!is_dir(SPIDER_PATH_AFTER_TREATING)) {
            mkdir(SPIDER_PATH_AFTER_TREATING);
        }
        // 转移
        rename($spider_result_path. "/$spider_result_name", SPIDER_PATH_AFTER_TREATING. "/$spider_result_name");
    }
}

//以gbk对字符串进行编码
function urlencodegbk($key){  
        $num  = mb_strlen($key,"gbk");  
        $num1 = mb_strlen($key,"utf-8");  
        if($num > $num1){  
            $key = iconv("utf-8","gbk//ignore",$key);  
        }  
        return urlencode($key);  
} 

//以utf8对字符串进行编码
function urlencodeutf8($key){  
        $num  = mb_strlen($key,"gbk");  
        $num1 = mb_strlen($key,"utf-8");  
        if($num <= $num1){  
            $key = iconv("gbk","utf-8//ignore",$key);  
        }  
        return urlencode($key);  
}