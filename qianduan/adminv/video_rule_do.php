<?php
require_once('inc_dbconn.php');
$op = $_GET["op"];
$time = time();
switch($op){
	case 'add':
		$url=trim($_POST['url']);
        $rule_name=trim($_POST['rule_name']);
		$title_b=trim($_POST['title_b']);
		$title_e=trim($_POST['title_e']);
		$time_b=trim($_POST['pubtime_b']);
		$time_e=trim($_POST['pubtime_e']);
		$time_format=trim($_POST['time_format']);
		$media_b=trim($_POST['media_b']);
		$media_e=trim($_POST['media_e']);
		$channel_b=trim($_POST['channel_b']);
		$channel_e=trim($_POST['channel_e']);
		$summary_b=trim($_POST['summary_b']);
		$summary_e=trim($_POST['summary_e']);

		$sql="insert into video_article_rule(site_url,rule_name,title_b,title_e,time_b,time_e,time_format,media_b,media_e,channel_b,channel_e,summary_b,summary_e) values('$url','$rule_name','$title_b','$title_e','$time_b','$time_e','$time_format','$media_b','$media_e','$channel_b','$channel_e','$summary_b','$summary_e')";
		if(mysql_query($sql))
		{
			$sql = "insert into auto_work(aw_type,aw_time) values(2,$time)";
			mysql_query($sql);
			echo "<script>alert('添加成功!');location.href='video_rule_list.php';</script>";	
		}else{
			echo "<script>alert('添加失败!请重试');location.href='add_video_rule.php';</script>";
		}
	break;
        
    case 'del':
        $r_id=$_GET['r_id'];
        $sql="delete from video_article_rule where r_id=$r_id";
        if(mysql_query($sql)){
            $sql = "insert into auto_work(aw_type,aw_time) values(2,$time)";
			mysql_query($sql);
			echo "<script>alert('删除成功');location.href='video_rule_list.php';</script>";	
		}else{		
			echo "<script>alert('删除失败');location.href='video_rule_list.php';</script>";
		}
    break;
    
    case 'edit':
        $r_id=$_POST['r_id'];
        
		$url=trim($_POST['url']);
        $rule_name=trim($_POST['rule_name']);
		$title_b=trim($_POST['title_b']);
		$title_e=trim($_POST['title_e']);
		$time_b=trim($_POST['pubtime_b']);
		$time_e=trim($_POST['pubtime_e']);
		$time_format=trim($_POST['time_format']);
		$media_b=trim($_POST['media_b']);
		$media_e=trim($_POST['media_e']);
		$channel_b=trim($_POST['channel_b']);
		$channel_e=trim($_POST['channel_e']);
		$summary_b=trim($_POST['summary_b']);
		$summary_e=trim($_POST['summary_e']);

		
        $sql="update video_article_rule set site_url='$url',rule_name='$rule_name',title_b='$title_b',title_e='$title_e',time_b='$time_b',time_e='$time_e',time_format='$time_format',media_b='$media_b',media_e='$media_e',channel_b='$channel_b',channel_e='$channel_e',summary_b='$summary_b',summary_e='$summary_e' where r_id=$r_id";
        if(mysql_query($sql))
        {
            $sql = "insert into auto_work(aw_type,aw_time) values(2,$time)";
			mysql_query($sql);
			echo "<script language='javascript'>alert('编辑成功');window.location.href='video_rule_list.php'</script>";	
        }else{
            echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='edit_video_rule.php?r_id=$r_id'</script>";
        }     
    break;
}

?>