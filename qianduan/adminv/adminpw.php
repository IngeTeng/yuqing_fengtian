<?php
/**
 * 管理员修改密码界面
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 * @informaition  
 */

	require_once('admincheck.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> 修改密码 </title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="author" content="Jiangting@WiiPu -- http://www.wiipu.com" />
  <link rel="stylesheet" href="style2.css" type="text/css"/>
 </head>
 <body>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：首页 －&gt;  <strong>修改密码</strong></span>
		</div>
		<div class="header2"><span>修改密码</span></div>
		<div class="fromcontent">
			<form action="adminpw_do.php" method="post" id="doForm">
				<p>　原密码：<input class="in1" type="password" name="pwd1"/> <span class="start">*</span></p>
				<p>　新密码：<input class="in1" type="password" name="pwd2"/> <span class="start">* 不少于6个字符</span></p>
				<p>确认密码：<input class="in1" type="password" name="pwd3"/> <span class="start">*</span></p>
				<div class="btn"><input type="image" src="images/submit1.gif" width="56" height="20" alt="提交" onClick="return check();"/></div>
				<script type="text/javascript">
					function check(){
						var f=document.getElementById('doForm');
						if(f.pwd1.value=="")
						{
							alert('原密码不能为空');
							f.pwd1.focus();
							return false;
						}
						if(f.pwd2.value=="")
						{
							alert('新密码不能为空');
							f.pwd2.focus();
							return false;
						}
						if(f.pwd2.value.length<6)
						{
							alert('新密码不少于6个字符');
							f.pwd2.focus();
							return false;
						}
						if(f.pwd3.value=="")
						{
							alert('确认密码不能为空');
							f.pwd3.focus();
							return false;
						}
						if(f.pwd2.value!=f.pwd3.value)
						{
							alert('两次输入的密码不一致');
							f.pwd2.value="";
							f.pwd3.value="";
							return false;
						}
					}
				</script>
			</form>
		</div>
	</div>
 </body>
</html>
