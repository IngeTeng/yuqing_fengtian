<?php
include_once('adminv/inc_dbconn.php');
$uk_id=$_POST['uk_id'];
$k_id=$_POST['k_id'];
$now=time();
$query="select add_time from user_keywords where uk_id=$uk_id";
$res=mysql_query($query);
$row=mysql_fetch_array($res);
$add_time=$row['add_time'];
//if($now-$add_time<=2160000){
 //  $rs['result']= "fail";
  // echo json_encode($rs);
  // return;
//}
$delete="delete from user_keywords where uk_id=$uk_id";	
if(mysql_query($delete)){
     $query="select uk_id from user_keywords where k_id=$k_id"; 
	 $res=mysql_query($query);
	 $num=mysql_num_rows($res);
	 if($num==0){
	     $delete="delete from keyword where k_id=$k_id";
		 mysql_query($delete);
		 $insert="insert into auto_work(aw_type,aw_time) values(4,$now)";
		 mysql_query($insert);
		 $insert="insert into auto_work(aw_type,aw_time) values(104,$now)";
		 mysql_query($insert);
		 $delete="delete from weibo_task where k_id=$k_id";
		 mysql_query($delete);
	 } 
     $rs['result']= "success";
     echo json_encode($rs);
}
?>