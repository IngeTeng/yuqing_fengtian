<?php
require_once('inc_dbconn.php');
$r_id=$_POST['r_id'];
$sql="select * from news_article_rule where r_id=$r_id";
$res=mysql_query($sql);
$row=mysql_fetch_array($res);
echo json_encode($row);
?>