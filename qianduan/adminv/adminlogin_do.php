<?php
/**
 * 管理员登录
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
		require_once('inc_dbconn.php');
		$admin_account=sqlReplace(trim($_POST['name']));

		$admin_pwd=sqlReplace(trim($_POST['pwd']));
		checkData($admin_account,"账号",1);
		checkData($admin_pwd,"密码",1);
		$code=trim($_POST['code']);
		if($code!=$_SESSION['wii_imgcode'])
		{
			alertInfo('验证码错误',"adminLogin.html",0);
		}
		$sql="select * from ".WIIDBPRE."_admin where admin_account='".$admin_account."' and admin_password='".md5($admin_pwd)."'";
		$result=mysql_query($sql)or die('未知原因查询失败');
		$row=mysql_fetch_array($result);
		if($row)
		{
			$_SESSION['wii_admin_account']=$admin_account;
			$ip=$_SERVER['REMOTE_ADDR'];
			$logincount=$row['admin_loginCount']+1;
			$sql2="update ".WIIDBPRE."_admin set admin_logintime=now(),admin_loginip='".$ip."',admin_logincount=".$logincount." where admin_account='".$admin_account."'";
			mysql_query($sql2)or die('未知原因更新失败');
			Header("Location:adminindex.php");
		}else{
			alertInfo('账户或密码错误',"adminlogin.html",0);
		}
?>