<?php
require_once('inc_dbconn.php');
$op = $_GET["op"];
switch($op){
	case 'add':
		$c_name=trim($_POST['c_name']);
        $full_domain=trim($_POST['full_domain']);
		$sql="insert into channel_list(c_name,full_domain) values('$c_name','$full_domain')";
		if(mysql_query($sql))
		{	
			echo "<script>alert('添加成功!');location.href='channel_list.php';</script>";	
		}else{
			echo "<script>alert('添加失败!请重试');location.href='add_channel.php';</script>";
		}
	break;
        
    case 'del':
        $c_id=$_GET['c_id'];
        $sql="delete from channel_list where c_id=$c_id";
        if(mysql_query($sql)){
			echo "<script>alert('删除成功');location.href='channel_list.php';</script>";	
		}else{		
			echo "<script>alert('删除失败');location.href='channel_list.php';</script>";
		}
    break;
    
    case 'edit':
        $c_id=$_POST['c_id'];
		$c_name=trim($_POST['c_name']);
        $full_domain=trim($_POST['full_domain']);

       $sql="update channel_list set c_name='$c_name',full_domain='$full_domain' where c_id=$c_id";
        if(mysql_query($sql))
        {
			echo "<script language='javascript'>alert('编辑成功');window.location.href='channel_list.php'</script>";	
        }else{
            echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='edit_channel.php?c_id=$c_id'</script>";
        }     
    break;
}

?>