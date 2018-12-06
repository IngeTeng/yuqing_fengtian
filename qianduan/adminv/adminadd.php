<?php
/**
 * 管理员添加界面
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
	require_once('admincheck.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> 添加管理员 </title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="author" content="Jiangting@WiiPu -- http://www.wiipu.com" />
  <link rel="stylesheet" href="style2.css" type="text/css"/>
 </head>
 <body>
   <div class="bgintor">
	<div class="tit1">
		<ul>
			<li class="l1"><a href="adminList.php" target="mainFrame" >管理员列表</a> </li>
			<li><a href="adminadd.php">添加管理员</a> </li>
		</ul>		
	</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：用户管理 －&gt; <strong>添加管理员</strong></span>
		</div>
		<div class="header2"><span>添加管理员</span>
		</div>
		<div class="fromcontent">
			<form action="admin_do.php?act=add" method="post" id="doForm">
				<p>管理员帐号：<input class="in1" type="text" name="admin_account"/> <span class="start">* 不少于4个字符</span></p>
				<p>　　　密码：<input class="in1" type="password" name="admin_pwd"/> <span class="start">* 不少于6个字符</span></p>
				<p>　确认密码：<input class="in1" type="password" name="admin_pwd2"/> <span class="start">*</span></p>
				<div class="btn">
					<input type="image" src="images/submit1.gif" width="56" height="20" alt="提交" onClick="return check();"/>
				</div>
				<script type="text/javascript">
					function check(){
						var f=document.getElementById('doForm');
						if(f.admin_account.value=="")
						{
							alert('管理员帐号不能为空');
							f.admin_account.focus();
							return false;
						}
						if(f.admin_account.value.length<4)
						{
							alert('管理员帐号不少于4个字符');
							f.admin_account.focus();
							return false;
						}
						if(f.admin_pwd.value=="")
						{
							alert('管理员密码不能为空');
							f.admin_pwd.focus();
							return false;
						}
						if(f.admin_pwd.value.length<6)
						{
							alert('管理员密码不少于6个字符');
							f.admin_pwd.focus();
							return false;
						}
						if(f.admin_pwd.value!=f.admin_pwd2.value)
						{
							alert('两次输入的密码不一致');
							f.admin_pwd.value="";
							f.admin_pwd2.value="";
							f.admin_pwd.focus();
							return false;
						}
					}
				</script>
			</form>
		</div>
	</div>
  </div>
 </body>
</html>
