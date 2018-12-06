<?php
include_once('adminv/inc_dbconn.php');
if(!isset($_COOKIE['user_id']))
{
   $rs['result']= "fail";
   echo json_encode($rs);
   return;
}
$user_id = $_COOKIE['user_id'];
$c_id=$_POST['c_id'];
if ($c_id > 0)
{
	$now=time();
	$query="select user_id from user_category where c_id=$c_id";
	$res=mysql_query($query);
	if (mysql_num_rows($res) <= 0)
	{
		$rs['result']= "fail";
   		echo json_encode($rs);
   		return;
	}
	$row = mysql_fetch_array($res);
	$uid = $row["user_id"];
	if ($uid != $user_id)
	{
		$rs['result']= "fail";
   		echo json_encode($rs);
   		return;
	}
	mysql_free_result($res);
}
$uk_id = $_POST["uk_id"];
$update="update user_keywords set c_id = $c_id where uk_id = $uk_id";	
if(mysql_query($update)){
     $rs['result']= "success";
     echo json_encode($rs);
}
else
{
	$rs['result']= "fail";
   echo json_encode($rs);
}
?>