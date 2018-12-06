<?php
	require_once('../config.php');
	require_once('function.php');

	ini_set('display_errors',1);            //错误信息  
	ini_set('display_startup_errors',1);    //php启动错误信息  
	error_reporting(-1);                    //打印出所有的 错误信息  
	ini_set('error_log', dirname(__FILE__) . '/test_log.txt'); //将出错信息输出到一个文本文件 
	$str = '和平使命2018军演将有问题广汽丰田，致炫，丰田，卡罗拉，缺陷充分参考俄军在叙实战经验凯美瑞吐槽';
	$title = iconv('utf-8', 'GB18030', $str );

    //$k_ids = article_keyword(KEYWORD_HOST, KEYWORD_PORT, $title, $title);print_r($k_ids);
    $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $str);print_r($article_property);
	// $spider_url = "http://weixin.sogou.com/?usip=&query=%E5%87%AF%E7%BE%8E%E7%91%9E&ft=&tsn=1&et=&interation=&type=2&wxid=&page=2&ie=utf8";
	// $header = get_headers($spider_url,1);
	// $spider_result = get_html($spider_url);     // 获取html
	// // $proxy_infos = get_proxy();
	// file_put_contents('11111', $spider_result);
	// print_r($header);
    //print_r($spider_result);
 //    $proxy_str = json_encode($proxy_infos);
 //    file_put_contents(date('Y_m_d'), $proxy_str);



	// 获取的html,带模拟登陆
// function get_html($url, $cookie='', $proxy='', $proxy_port='', $referer='', $gzip=false) {
//     $ch = curl_init();
//     // 设置选项，包括URL
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
//     //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//允许页面跳转，获取重定向
//     curl_setopt($ch, CURLOPT_HEADER, 0);
//     curl_setopt($ch, CURLOPT_TIMEOUT, 60);      // 60秒超时
//     if($gzip) curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // 编码格式

//     if($cookie != '') {
//     	$coo = "Cookie:$cookie";
//     	$headers[] = $coo;
//     	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     }
//     if($referer != '') {
//         curl_setopt($ch, CURLOPT_REFERER, $referer);
//     }
//     if($proxy != '' and $proxy_port != '') {
//         curl_setopt($ch, CURLOPT_PROXY, $proxy);
//         curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
//         curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
//     }
    
//     // 获取内容
//     $output = curl_exec($ch);
//     curl_close($ch);
//     return $output;
// }
?>