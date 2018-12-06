<?php
require_once('inc_dbconn.php');
$op = $_GET["op"];
switch($op){
	case 'add':
		$media_name=trim($_POST['media_name']);
        $domain=trim($_POST['domain']);
        $grade=$_POST['grade'];
		$sql="insert into media_list(media_name,domain,grade) values('$media_name','$domain',$grade)";
		if(mysql_query($sql))
		{	
			echo "<script>alert('添加成功!');location.href='media_list.php';</script>";	
		}else{
			echo "<script>alert('添加失败!请重试');location.href='add_media.php';</script>";
		}
	break;
        
    case 'del':
        $m_id=$_GET['m_id'];
        $sql="delete from media_list where m_id=$m_id";
        if(mysql_query($sql)){
			echo "<script>alert('删除成功');location.href='media_list.php';</script>";	
		}else{		
			echo "<script>alert('删除失败');location.href='media_list.php';</script>";
		}
    break;
    
    case 'edit':
        $m_id=$_POST['m_id'];
		$media_name=trim($_POST['media_name']);
        $domain=$_POST['domain'];
        $grade=$_POST['grade'];

       $sql="update media_list set media_name='$media_name',domain='$domain',grade=$grade where m_id=$m_id";
        if(mysql_query($sql))
        {
			echo "<script language='javascript'>alert('编辑成功');window.location.href='media_list.php'</script>";	
        }else{
            echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='edit_media.php?m_id=$m_id'</script>";
        }     
    break;
}

?>