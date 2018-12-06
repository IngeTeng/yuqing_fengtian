<?php
require_once('adminv/inc_dbconn.php');
$title=$_POST['article_title'];
$query="select article_title,article_content,article_url,article_id from news_article where article_title='$title'";
$res=mysql_query($query);
$i=0;
while($rows=mysql_fetch_array($res)){
   $result[$i]['title']=$rows['article_title'];
   $result[$i]['content']=$rows['article_content'];
   $result[$i]['url']=$rows['article_url'];
   $result[$i]['article_id']=$rows['article_id'];
   $i++;
}
echo json_encode($result);
?>