<?php
include_once('adminv/inc_dbconn.php');

$user_id=$_COOKIE['user_id'];
$sql = "delete from news_search where user_id = ". $user_id;
mysql_query($sql);
$result = array($sql);
echo(json_encode($result));
?>