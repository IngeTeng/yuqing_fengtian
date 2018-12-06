<?php

/**
 * @filename add_weixin_c.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-10-15  10:56:59
 * @updatetime 2016-10-15  10:56:59
 * @version 1.0
 * @Description
 * 添加微信cookie
 * 
 */

require_once('admincheck.php');
require_once('inc_dbconn.php');
$title = '添加cookie';
$sql = 'SELECT * FROM weixin_cookies ORDER BY status desc';
$result=mysql_query($sql);					
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title> <?php echo $title;?> </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="author" content="liuxiao@WiiPu -- http://www.wiipu.com" />
		<link rel="stylesheet" href="style2.css" type="text/css"/>
	</head>
	<body>
		<div class="bgintor">
		<div class="tit1">
			<ul>
                                <li class="l1"><a href="weixin_cookies.php"><?php echo 'cookies列表';?></a> </li>
				<li><a href="#"><?php echo $title;?></a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：媒体管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span>
		</div>
		<div class="fromcontent">
                        <form action="weixin_c_do.php?op=add" method="post">
				<p>cookie：</p>
                                    <textarea name="cookie"></textarea>
				<div class="btn">
					<input type="image" src="images/submit1.gif" width="56" height="20" alt="提交"/>
				</div>
			</form>
		</div>
	</div>
  </div>
 </body>
</html>
