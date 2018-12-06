<?php
/**
 * 检查管理员是否登录
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
	require_once('inc_configue.php');
	if(empty($_SESSION['wii_admin_account'])){
		echo "<script language='javascript'>top.location.href='adminlogin.html'</script>";
		die('禁止访问');
	}
	else
	{
		echo "<script language='javascript'>top.location.href='adminindex.php'</script>";

	}
?>