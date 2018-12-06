<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="修改用户密码";
	$user_id=$_GET['user_id'];
	$query="select email from user where user_id=$user_id";
	$res=mysql_query($query);
	$row=mysql_fetch_array($res);
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
			<span>位置：用户管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span>
		</div>
		<div class="fromcontent">
			<form action="user_do.php?act=edit_pass" method="post">
				<p>用户名（邮箱）：<?php echo $row['email'];?></p>
				<p>新密码：<input class="in1" type="password" name="password" id="password" value=""/></p>
				<div class="btn">
				    <input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
					<input type="image" src="images/submit1.gif" width="56" height="20" alt="提交"/>
				</div>
			</form>
		</div>
	</div>
  </div>
 </body>
</html>
