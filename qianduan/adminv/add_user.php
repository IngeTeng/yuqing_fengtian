<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="添加用户";
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
				<li><a href="#"><?php echo $title;?></a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：系统管理 －&gt;会员管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span>
		</div>
		<div class="fromcontent">
			<form action="user_do.php?act=add" method="post">
				<p>用户名（邮箱）：<input class="in1" type="text" name="user_name" id="user_name"/></p>
				<p>用户类型：
				     <select name="user_type">
					    <option value="0">普通用户</option>
						<option value="1">特殊用户</option>
						<option value="2">演示用户</option>
					 </select>
				</p>
				<p>用户密码：<input class="in1" type="text" name="password" id="password"/></p>
				<p>关键字数目：<input class="in1" type="text" name="key_num" id="key_num"/></p>
				<div class="btn">
					<input type="image" src="images/submit1.gif" width="56" height="20" alt="提交"/>
				</div>
			</form>
		</div>
	</div>
  </div>
 </body>
</html>
