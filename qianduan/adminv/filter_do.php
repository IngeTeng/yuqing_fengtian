<?php

/**
 * @filename ffilter_do.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-10-3  12:59:56
 * @updatetime 2016-10-3  12:59:56
 * @version 1.0
 * @Description
 * 
 */

require_once('inc_dbconn.php');
require_once('admincheck.php');
$op = $_GET["op"];
$time=time()+2160000;
switch($op){
	case 'add':
		$k_id=trim($_POST['keyword']);
		$filter_word=$_POST['filter_word'];
                echo $k_id, PHP_EOL;
                echo $filter_word;
		$sql="INSERT INTO keyword_filter(k_id, filter_word) VALUES('$k_id','$filter_word')";
		if(mysql_query($sql))
		{	
			echo "<script>alert('添加成功!');location.href='filter_keyword.php';</script>";	
		}else{
			echo "<script>alert('添加失败!请重试');location.href='add_filter.php';</script>";
		}
	break;
        
    case 'del':
        $f_id=$_GET['id'];
        $sql="DELETE FROM keyword_filter WHERE id=$f_id";
        if(mysql_query($sql)) {
                echo "<script>alert('删除成功');location.href='filter_keyword.php';</script>";	
        } 
        else {		
                echo "<script>alert('删除失败');location.href='filter_keyword.php';</script>";
        }
    break;
    
    case 'edit':
	    $f_id = $_POST['f_id'];
            $k_id = $_POST['keyword'];
            $filter_word = $_POST['filter_word'];
            
            $sql="UPDATE keyword_filter SET k_id='$k_id', filter_word='$filter_word' WHERE id=$f_id";
            if(mysql_query($sql)) {
                echo "<script language='javascript'>alert('编辑成功');window.location.href='filter_keyword.php'</script>";	
            }
            else {
                echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='edit_filter.php?id=$f_id'</script>";
            }     
    break;
}

?>