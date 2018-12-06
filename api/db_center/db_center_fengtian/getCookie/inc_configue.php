<?php
/**
 * 基本参数设置
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 * @informaition  
 */

	//error_reporting(0);    //网站开发必须关闭此处，网站上线必须打开此处
	header("content-type:text/html;charset=utf-8");
	session_start();
	ob_start();
	
	//配置数据库连接参数
	define('WIIDBHOST','172.26.133.67');
	define('WIIDBUSER','root');
	define('WIIDBPASS','*Wiipuyuqing#');
	define('WIIDBNAME','yuqing');
	define('WIIDBPRE','info');

?>
