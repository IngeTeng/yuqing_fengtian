<?php
/**
 * 数据库连接
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 * @informaition  
 */
	require_once('inc_configue.php');
	//require_once('inc_function.php');

	$db_connect=mysql_connect(WIIDBHOST,WIIDBUSER,WIIDBPASS);
	if (!$db_connect){
		die ('数据库连接失败');
	}
	mysql_select_db(WIIDBNAME, $db_connect) or die ("没有找到数据库。");
	mysql_query("set names utf8;");

?>