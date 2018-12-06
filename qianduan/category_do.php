<?php
include_once('adminv/inc_dbconn.php');
if(!isset($_COOKIE['user_id']))
{
   header("location : index.php");
   return;
}
$user_id = $_COOKIE['user_id'];
$cname=addslashes(trim($_POST['cname']));
$query="select user_type from user where user_id=$user_id";
$res=mysql_query($query);
$row=mysql_fetch_array($res);
$user_type=$row['user_type'];
$time=time();
$flag=0;
if($user_type==2)
{
       echo "<script>alert('您没有该权限！');location.href='index.php';</script>";  
	   return;
}
$flag = 1;
if($flag==1){
   $query="select c_id from user_category where category_name='$cname' and user_id = $user_id";
   $res=mysql_query($query);
   $num=mysql_num_rows($res);
   if($num==0){
         $insert="insert into user_category(user_id, category_name, add_time) values($user_id,'$cname',$time)";
         if(mysql_query($insert)){
           echo "<script>alert('添加成功!');location.href='category_list.php';</script>";
	    }
	    else
	    {
	    	echo "<script>alert('添加失败!');location.href='add_category.php';</script>";
	    }	
         
   }
   else
   {   
	    echo "<script>alert('您已添加过该分类，请勿重复添加!');location.href='add_category.php';</script>";       
   }
}else{
       echo "<script>alert('添加失败!');location.href='add_category.php';</script>";
}	
?>