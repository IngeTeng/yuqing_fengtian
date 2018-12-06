<?php
  if(!isset($_COOKIE['user_id'])){
      echo "<script>alert('您尚未登陆！');location.href='index.php';</script>";
      return;
  }
  include_once('adminv/inc_dbconn.php');
  $user_id=$_COOKIE['user_id'];
  $query="select user_type from user where user_id=$user_id";
  $res=mysql_query($query);
  $row=mysql_fetch_array($res);
  $user_type=$row['user_type'];


  if($user_type==2){
       echo "<script>alert('您没有权限访问！');location.href='index.php';</script>";
	   return;
  }
?>