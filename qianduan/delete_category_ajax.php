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

$delete="delete from user_category where c_id=$c_id";	
if(mysql_query($delete)){
     $query="update user_keywords set c_id = 0 where c_id = $c_id"; 
	 mysql_query($query);
     $rs['result']= "success";
     echo json_encode($rs);
}
else
{
	$rs['result']= "fail";
   echo json_encode($rs);
}
?>