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
		$content_b=trim($_POST['content_b']);
		$content_e=trim($_POST['content_e']);
		$time_b=trim($_POST['pubtime_b']);
		$time_e=trim($_POST['pubtime_e']);
		$time_format=trim($_POST['time_format']);
		$media_b=trim($_POST['media_b']);
		$media_e=trim($_POST['media_e']);
		$author_b=trim($_POST['author_b']);
		$author_e=trim($_POST['author_e']);
		$comment_b=trim($_POST['comment_b']);
		$comment_e=trim($_POST['comment_e']);
		$read_b=trim($_POST['read_b']);
		$read_e=trim($_POST['read_e']);

		$sql="insert into blog_article_rule(site_url,rule_name,title_b,title_e,content_b,content_e,time_b,time_e,time_format,media_b,media_e,author_b,author_e,comment_b,comment_e,read_b,read_e) values('$url','$rule_name','$title_b','$title_e','$content_b','$content_e','$time_b','$time_e','$time_format','$media_b','$media_e','$author_b','$author_e','$comment_b','$comment_e','$read_b','$read_e')";
		if(mysql_query($sql))
		{
			$sql = "insert into auto_work(aw_type,aw_time) values(2,$time)";
			mysql_query($sql);
			echo "<script>alert('添加成功!');location.href='blog_rule_list.php';</script>";	
		}else{
			echo "<script>alert('添加失败!请重试');location.href='add_blog_rule.php';</script>";
		}
	break;
        
    case 'del':
        $r_id=$_GET['r_id'];
        $sql="delete from blog_article_rule where r_id=$r_id";
        if(mysql_query($sql)){
            $sql = "insert into auto_work(aw_type,aw_time) values(2,$time)";
			mysql_query($sql);
			echo "<script>alert('删除成功');location.href='blog_rule_list.php';</script>";	
		}else{		
			echo "<script>alert('删除失败');location.href='blog_rule_list.php';</script>";
		}
    break;
    
    case 'edit':
        $r_id=$_POST['r_id'];
        
		$url=trim($_POST['url']);
        $rule_name=trim($_POST['rule_name']);
		$title_b=trim($_POST['title_b']);
		$title_e=trim($_POST['title_e']);
		$content_b=trim($_POST['content_b']);
		$content_e=trim($_POST['content_e']);
		$time_b=trim($_POST['pubtime_b']);
		$time_e=trim($_POST['pubtime_e']);
		$time_format=trim($_POST['time_format']);
		$media_b=trim($_POST['media_b']);
		$media_e=trim($_POST['media_e']);
		$author_b=trim($_POST['author_b']);
		$author_e=trim($_POST['author_e']);
		$comment_b=trim($_POST['comment_b']);
		$comment_e=trim($_POST['comment_e']);
		$read_b=trim($_POST['read_b']);
		$read_e=trim($_POST['read_e']);

		
        $sql="update blog_article_rule set site_url='$url',rule_name='$rule_name',title_b='$title_b',title_e='$title_e',content_b='$content_b',content_e='$content_e',time_b='$time_b',time_e='$time_e',time_format='$time_format',media_b='$media_b',media_e='$media_e',author_b='$author_b',author_e='$author_e',comment_b='$comment_b',comment_e='$comment_e',read_b='$read_b',read_e='$read_e' where r_id=$r_id";
        if(mysql_query($sql))
        {
            $sql = "insert into auto_work(aw_type,aw_time) values(2,$time)";
			mysql_query($sql);
			echo "<script language='javascript'>alert('编辑成功');window.location.href='blog_rule_list.php'</script>";	
        }else{
            echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='edit_blog_rule.php?r_id=$r_id'</script>";
        }     
    break;
}

?>