<?php
include_once('adminv/inc_dbconn.php');
$user_id=$_COOKIE['user_id'];
$old_pass=addslashes($_POST['old_pass']);
$new_pass1=addslashes($_POST['new_pass1']);
$new_pass2=addslashes($_POST['new_pass2']);
$query_pass="select password from user where user_id=$user_id";
$res=mysql_query($query_pass);
$row=mysql_fetch_array($res);
$password=$row['password'];
if($password!=md5($old_pass)){
      echo "<script>alert('原始密码不正确，请重新输入！');location.href='update_password.php';</script>";
}else{
     if($new_pass1==$new_pass2){ 
	       $new_pass=md5($new_pass1);   
           $update="update user set password='$new_pass' where user_id=$user_id";
           mysql_query($update);
		   echo "<script>alert('修改成功！请重新登陆!');location.href='index.php';</script>";
	 }else{
	     echo "<script>alert('两次密码输入不一致，请重新输入！');location.href='update_password.php';</script>";
	 }
}
?>