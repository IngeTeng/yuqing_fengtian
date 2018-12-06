<?php

// 当前路径
define("BASE_PATH", str_replace("\\", "/", realpath(dirname(__FILE__))));

$log_paths = array(
	BASE_PATH. "/data_center/log/", // 数据中心日志
	BASE_PATH. "/wii_spider/log/",	// 抓取中心
	BASE_PATH. '/wii_spider/app_list/log/',	// app_list
	BASE_PATH. '/wii_spider/appsearch_list/log/',	// appsearch_list
	BASE_PATH. '/wii_spider/bbs_list/log/',	// bbs_list
	BASE_PATH. '/wii_spider/bbs_article/log/',	// bbs_article
	BASE_PATH. '/wii_spider/blog_list/log/',	// blog_list
	BASE_PATH. '/wii_spider/news_list/log/',	// news_list
	BASE_PATH. '/wii_spider/video_list/log/',	// video_list
	BASE_PATH. '/wii_spider/weibo_list/log/',	// weibo_list
	BASE_PATH. '/wii_spider/weixin_list/log/',	// weixin_list
	BASE_PATH. '/wii_spider/zhidao_list/log/',	// zhidao_list
	
	// 无效的抓取文件
	BASE_PATH. '/wii_spider/news_list/spider_result/',
	BASE_PATH. '/wii_spider/video_list/spider_result/',
	BASE_PATH. '/wii_spider/app_list/spider_result/',
	BASE_PATH. '/wii_spider/appsearch_list/spider_result/',
	BASE_PATH. '/wii_spider/zhidao_list/spider_result/',
	BASE_PATH. '/wii_spider/bbs_list/spider_result/',
	BASE_PATH. '/wii_spider/weixin_list/spider_result/',
	BASE_PATH. '/wii_spider/weibo_list/spider_result/',
	BASE_PATH. '/wii_spider/blog_list/spider_result/',
	BASE_PATH. '/wii_spider/bbs_article/spider_result/',
	//BASE_PATH. '/wii_spider/result/',
	BASE_PATH. '/data_center/fail/',
);

$out_paths = array(
	BASE_PATH. "/data_center/nohup.out",
	BASE_PATH. "/wii_spider/nohup.out",
	BASE_PATH. "/wii_spider/output",
	BASE_PATH. '/wii_spider/old_spider_result/',
);
	
while(true) {
	foreach($log_paths as $log_path) {
		if(is_dir($log_path)) {
			$files = glob($log_path. "*");
			foreach($files as $file) {
				$strlen = strlen($log_path);
				$date = substr($file, $strlen, 10);
				$date = str_replace('_', '-', $date);
				$time = strtotime($date);
			
				if((time() - $time) > 259200) {	// 3天日志
					unlink($file);
					//echo $file. '<br>';
				}
			}
		}
	}
	foreach($out_paths as $out_path) {
		if(is_file($out_path)) {
			unlink($out_path);	
		}
	}

	$files = glob(BASE_PATH. "/wii_spider/include/warn/". "*");//报警器日志
	foreach($files as $file) {
		unlink($file);
	}
	$stime = 60*60*6;//6小时一删
	sleep($stime);
}