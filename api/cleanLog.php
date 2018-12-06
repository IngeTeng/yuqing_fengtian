<?php

// 当前路径
define("BASE_PATH", str_replace("\\", "/", realpath(dirname(__FILE__))));

$log_paths = array(
	BASE_PATH. "/content_center/log/", // 内容提取中心日志
	BASE_PATH. "/db_center/log/",	// 数据库中心
	BASE_PATH. '/task_center/log/',	// 任务中心
	BASE_PATH. '/task_center/proxy/',	// 代理文件
	BASE_PATH. "/content_center_pinjin/log/", // 内容提取中心日志
	BASE_PATH. "/db_center_pinjin/log/",	// 数据库中心
	BASE_PATH. '/task_center_pinjin/log/',	// 任务中心
	BASE_PATH. '/task_center_pinjin/proxy/',	// 代理文件
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
			
				if((time() - $time) > 86400) {	// 3天日志
					unlink($file);
					//echo $file. PHP_EOL;
				}
			}
		}
	}
	//break;
	sleep(86400);
}