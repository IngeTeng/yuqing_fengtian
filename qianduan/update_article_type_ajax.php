<?php
require_once('adminv/inc_dbconn.php');
$id=$_POST['a_id'];
$a_type=$_POST['a_type'];
$type=$_POST['at'];
if($a_type==1){
    $key_table="news_key";
}elseif($a_type==2){
    $key_table="bbs_key";
}elseif($a_type==3){
    $key_table="blog_key";
}elseif($a_type==4){
    $key_table="weibo_key";
}elseif($a_type==5){
    $key_table="video_key";
}elseif($a_type==6){
    $key_table="weixin_key";
}elseif($a_type==7){
    $key_table="zhidao_key";
}elseif($a_type==8){
    $key_table="app_key";
}
$time=time();
$update="update $key_table set a_type=$type where id=$id";
if(mysql_query($update)){
$rs['result']= "success";
}
echo json_encode($rs);
?>