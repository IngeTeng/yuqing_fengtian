<?php
include_once('adminv/inc_dbconn.php');
$user_id=$_POST['user_id'];
$file_id=$_POST['file_id'];
$delete="delete from file_list where file_id=$file_id";
if(mysql_query($delete)){
     $file=$user_id."/".$file_id.".xls";
	 unlink($file);
     $rs['result']= "success";
     echo json_encode($rs);
}
?>