<?php
/**
 * 管理员添加、删除
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
require_once('admincheck.php');
require_once('inc_dbconn.php');

$act=$_GET['act'];
switch($act)
{
	case 'add':
		//得到提交的数据，并进行过滤
		$adminaccount=sqlReplace(trim($_POST['admin_account']));
		$adminpwd=sqlReplace(trim($_POST['admin_pwd']));
		$adminpwd2=sqlReplace(trim($_POST['admin_pwd2']));

		//检验数据的合法性
		checkData($adminaccount,'管理员帐号',1);
		checkData($adminpwd,'管理员密码',1);
		checkData($adminpwd2,'确认密码',1);
		
		if($adminpwd!=$adminpwd2)
			alertInfo('两次密码不相同','adminadd.php',0);
		if(strlen($adminpwd)<6)
			alertInfo('管理员密码不少于6个字符','adminadd.php',0);

		$sql="select * from ".WIIDBPRE."_admin where admin_account='".$adminaccount."'";
		$result=mysql_query($sql);
		$row=mysql_fetch_assoc($result);
		if($row) 
			alertInfo('该管理员帐号已经存在，请重新输入','adminadd.php',0);
		else{
			$sql2="insert into ".WIIDBPRE."_admin(admin_account,admin_password) values('".$adminaccount."','".md5($adminpwd)."')";
			if(mysql_query($sql2))
				alertInfo('管理员添加成功','adminlist.php',0);
			else
				alertInfo('未知原因添加失败，请重试。','adminadd.php',0);
		}
		break;
	case 'del':
		$id=sqlReplace(trim($_GET['id']));
		$id=checkData($id,"ID",0);
		$sql="select * from ".WIIDBPRE."_admin where admin_id=".$id;
		$result=mysql_query($sql);
		$row=mysql_fetch_assoc($result);
		if(!$row)
			alertInfo('您要删除的管理员不存在','adminlist.php',0);
		else if($row['admin_account']==$_SESSION['wii_admin_account'])
			alertInfo('不能删除当前登录的管理员','adminlist.php',0);
		else{
			$sql2="delete from ".WIIDBPRE."_admin where admin_id=".$id;
			if(mysql_query($sql2))
				alertInfo('删除管理员成功','adminlist.php',0);
			else
				alertInfo('删除管理员失败，原因SQL出现异常','adminlist.php',0);
		}
		break;

}
?>