<?php
require_once('adminv/inc_dbconn.php');
$id=$_POST['id'];
$delete="delete from news_search where article_id=$id";
if(mysql_query($delete)){
$rs['result']= "success";
}
echo json_encode($rs);
?>