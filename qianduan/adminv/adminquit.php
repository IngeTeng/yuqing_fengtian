<?php
/**
 * 管理员安全退出
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 * @informaition  
 */
	session_start();
	session_destroy();
	echo "<script language='javascript'>top.location.href='adminlogin.html'</script>";
?>