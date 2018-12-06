<?php
include_once('adminv/inc_dbconn.php');
$user_name=addslashes($_POST['user_name']);
$password=addslashes($_POST['password']);
$query_name="select user_id,password,user_type,status from user where email='$user_name'";
$res=mysql_query($query_name);
$num=mysql_num_rows($res);
if($num==0){
     echo "<script>alert('用户不存在，请检查！');location.href='index.php';</script>";
}else{    
     $row=mysql_fetch_array($res);
	 $password_db=$row['password'];
	 $status=$row['status'];
	 $user_type=$row['user_type'];
	 $user_id=$row['user_id'];
	 if($password_db==md5($password)){
	     if($status==0){
		      setcookie("user_id",$user_id);
			  if($user_type==2){
			       echo "<script>location.href='show_home.php';</script>";
			  }else{
			       echo "<script>location.href='ordinary_home.php';</script>";
			  }
		 }elseif($status==1){
		      echo "<script>alert('由于欠费或其他原因导致您的账号异常，请联系管理员！');location.href='index.php';</script>";
		 }
	 }else{ 
	     echo "<script>alert('密码错误，请检查！');location.href='index.php';</script>";
	 }
}
?>