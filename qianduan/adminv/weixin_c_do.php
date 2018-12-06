<?php

/**
 * @filename weixin_c_do.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-10-15  11:11:30
 * @updatetime 2016-10-15  11:11:30
 * @version 1.0
 * @Description
 * 处理微信cookies的各种操作
 * 
 */

require_once('inc_dbconn.php');
require_once('admincheck.php');
$op = $_GET["op"];
$time=time()+2160000;
switch($op){
	case 'add':
		$cookie=trim($_POST['cookie']);
//                echo $cookie, PHP_EOL;
		$sql="INSERT INTO weixin_cookies (cookie) VALUES('$cookie')";
		if(mysql_query($sql))
		{	
			echo "<script>alert('添加成功!');location.href='weixin_cookies.php';</script>";	
		}else{
			echo "<script>alert('添加失败!请重试');location.href='add_weixin_c.php';</script>";
		}
	break;
        
        case 'edit':
	    $c_id = $_POST['c_id'];
            $weixin_cookie = $_POST['cookie'];
            $status = $_POST['status'];
            $sql="UPDATE weixin_cookies SET cookie='$weixin_cookie',status='$status' WHERE id=$c_id";
            if(mysql_query($sql)) {
                echo "<script language='javascript'>alert('编辑成功');window.location.href='weixin_cookies.php'</script>";	
            } else{
                echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='edit_weixin_c.php?c_id=$c_id'</script>";
            }     
        break;
        
        case 'del':
            $c_id=$_GET['id'];
            $sql="DELETE FROM weixin_cookies WHERE id=$c_id";
            if(mysql_query($sql)) {
                    echo "<script>alert('删除成功');location.href='weixin_cookies.php';</script>";	
            } 
            else {		
                    echo "<script>alert('删除失败');location.href='weixin_cookies.php';</script>";
            }
        break;
    
}

?>