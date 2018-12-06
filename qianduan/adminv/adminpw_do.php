<?php
/**
 * 管理员修改密码
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 * @informaition  
 */
	require_once('admincheck.php');
	require_once('inc_dbconn.php');

	$pw=sqlReplace(trim($_POST['pwd1']));
	$pw1=sqlReplace(trim($_POST['pwd2']));
	$pw2=sqlReplace(trim($_POST['pwd3']));

	checkData($pw,"原密码",1);
	checkData($pw1,"新密码",1);
	checkData($pw2,"新密码确认",1);

	if($pw1!=$pw2){
		alertInfo('两次密码输入不一致',"adminpw.php",0);
	}
	if(strlen($pw1)<6)
		alertInfo('新密码不少于6个字符','adminpw.php',0);


	$sql="select * from ".WIIDBPRE."_admin where admin_account='".$_SESSION['wii_admin_account']."' and admin_password='".md5($pw)."'";
	$rs=mysql_query($sql);
	$row=mysql_fetch_assoc($rs);
	if($row){
		$pw2=md5($pw2);
		$sql2="update ".WIIDBPRE."_admin set admin_password='".$pw2."' where admin_account='".$_SESSION['wii_admin_account']."'";
		mysql_query($sql2);
		alertInfo('修改密码成功，下次登录生效!',"adminpw.php",0);
	}else{
		alertInfo('原密码错误',"adminpw.php",0);
	}
?>