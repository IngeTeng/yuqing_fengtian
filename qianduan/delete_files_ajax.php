<?php
include_once('adminv/inc_dbconn.php');
$user_id=$_POST['user_id'];
$fids=$_POST['fids'];
$delete="delete from file_list where file_id in ($fids) and user_id = ".$user_id;

if(mysql_query($delete))
{
    // $file=$user_id."/".$file_id.".xls";
	// unlink($file);
     $rs['result']= "success";
     echo json_encode($rs);
}
?>