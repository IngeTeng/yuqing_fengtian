<?php
require_once('adminv/inc_dbconn.php');
$title=addslashes(trim($_POST['title']));
$content=addslashes(trim($_POST['content']));
$time=time();
$user_id=$_POST['user_id'];
$insert="insert into news_article_index(article_title,article_content,add_time,user_id) values('$title','$content',$time,$user_id)";
if(mysql_query($insert)){
    echo "<script>location.href='news_article_list.php';</script>";
}else{
    echo "<script>alert('添加失败');location.href='news_article_list.php';</script>";
}

?>