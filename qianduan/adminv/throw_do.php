<?php
require_once('inc_dbconn.php');
$op = $_GET["op"];
switch($op){
	case 'add':
        $throw=trim($_POST['throw']);
		$sql="insert into throw_url_list(url) values('$throw')";
		if(mysql_query($sql))
		{	
			echo "<script>alert('添加成功!');location.href='throw_list.php';</script>";	
		}else{
			echo "<script>alert('添加失败!请重试');location.href='add_throw.php';</script>";
		}
	break;
        
    case 'del':
        $id=$_GET['id'];
        $sql="delete from throw_url_list where id=$id";
        if(mysql_query($sql)){
			echo "<script>alert('删除成功');location.href='throw_list.php';</script>";	
		}else{		
			echo "<script>alert('删除失败');location.href='throw_list.php';</script>";
		}
    break;
    
    case 'edit':
        $id=$_POST['id'];
        $throw=trim($_POST['throw']);

       $sql="update throw_url_list set url='$throw' where id=$id";
        if(mysql_query($sql))
        {
			echo "<script language='javascript'>alert('编辑成功');window.location.href='throw_list.php'</script>";	
        }else{
            echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='edit_throw.php?id=$id'</script>";
        }     
    break;
}

?>