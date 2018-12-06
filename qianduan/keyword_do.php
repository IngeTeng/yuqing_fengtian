<?php
include_once('adminv/inc_dbconn.php');
$user_id=$_POST['user_id'];
$keyword=addslashes(trim($_POST['keyword']));
$query="select user_type from user where user_id=$user_id";
$res=mysql_query($query);
$row=mysql_fetch_array($res);
$user_type=$row['user_type'];
$time=time();
$flag=0;
if($user_type==2){
       echo "<script>alert('您没有该权限！');location.href='index.php';</script>";  
	   return;
}
if($user_type==0){
   $query="select key_num from user where user_id=$user_id";
   $res=mysql_query($query);
   $row=mysql_fetch_array($res);
   $key_num_up=$row['key_num'];
   $query="select k_id from user_keywords where user_id=$user_id";
   $res=mysql_query($query);
   $key_num=mysql_num_rows($res);
   if($key_num>=$key_num_up){
         echo "<script>alert('您的关键字数目已达到上限，若想继续添加，请联系管理！');location.href='add_keyword.php';</script>";
	     return;
   }else{
         $flag=1;
   }
}else{
         $flag=1;
}
if($flag==1){
   $query="select k_id from keyword where keyword='$keyword'";
   $res=mysql_query($query);
   $num=mysql_num_rows($res);
   if($num==0){
         $insert="insert into keyword(keyword) values('$keyword')";
         if(mysql_query($insert)){
            $k_id=mysql_insert_id();
			$insert="insert into auto_work(aw_type,aw_time) values(4,$time)";
			mysql_query($insert);
			$insert="insert into auto_work(aw_type,aw_time) values(104,$time)";
			mysql_query($insert);
			$insert="insert into weibo_task(k_id) values($k_id)";
			mysql_query($insert);
	        $insert="insert into user_keywords(user_id,k_id,add_time) values($user_id,$k_id,$time)";
	        if(mysql_query($insert)){
	                echo "<script>alert('添加成功!');location.href='keyword_list.php';</script>";
	        }else{
	                echo "<script>alert('添加失败!');location.href='add_keyword.php';</script>";
	        }	
         }
   }else{
           $row=mysql_fetch_array($res);
           $k_id=$row['k_id'];
	       $time=time();
	       $query="select uk_id from user_keywords where user_id=$user_id and k_id=$k_id";
           $res=mysql_query($query);
           $num=mysql_num_rows($res);
	       if($num==0){
	               $insert="insert into user_keywords(user_id,k_id,add_time) values($user_id,$k_id,$time)";
	               if(mysql_query($insert)){
	                      echo "<script>alert('添加成功!');location.href='keyword_list.php';</script>";
	               }else{
	                      echo "<script>alert('添加失败!');location.href='add_keyword.php';</script>";
	               }
	       }else{
	           echo "<script>alert('您已添加该关键字，请勿重复添加!');location.href='add_keyword.php';</script>";
	       }
    }
}else{
       echo "<script>alert('添加失败!');location.href='add_keyword.php';</script>";
}	
?>