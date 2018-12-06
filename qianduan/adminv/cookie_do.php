<?php
require_once('inc_dbconn.php');
$op = $_GET["op"];
$time=time()+2160000;
switch($op){
	case 'add':
		$qq_number=$_POST['qq_number'];
		$qq_cookie=trim($_POST['qq_cookie']);
		$sql="insert into qq_cookies(qq_number,qq_cookie,expries)values('$qq_number','$qq_cookie',$time)";
		if(mysql_query($sql))
		{	
			echo "<script>alert('添加成功!');location.href='qqlist.php';</script>";	
		}else{
			echo "<script>alert('添加失败!请重试');location.href='add_qq.php';</script>";
		}
	break;
        
    case 'del':
        $qc_id=$_GET['qc_id'];
        $sql="delete from qq_cookies where qc_id=$qc_id";
        if(mysql_query($sql)){
			echo "<script>alert('删除成功');location.href='qqlist.php';</script>";	
		}else{		
			echo "<script>alert('删除失败');location.href='qqlist.php';</script>";
		}
    break;
    
    case 'edit':
	    $qc_id=$_POST['qc_id'];
        $qq_number=$_POST['qq_number'];
		$qq_cookie=$_POST['qq_cookie'];
       $sql="update qq_cookies set qq_number='$qq_number',qq_cookie='$qq_cookie',expries=$time, status=1 where qc_id=$qc_id";
        if(mysql_query($sql))
        {
			echo "<script language='javascript'>alert('编辑成功');window.location.href='qqlist.php'</script>";	
        }else{
            echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='edit_cookie.php?qc_id=$qc_id'</script>";
        }     
    break;
}

?>